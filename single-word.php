/**
     * Process single word
     * @param string $word - word to process. Check if word exists in db and get definitions and related words
     * @param bool $getWord - if true word will be retrieved from wordnik. If false that step will be omitted
     * @param bool $addRelated - if true related words will be searched
     * @param bool $addSynonyms
     * @param bool $checkDefinition - if true definitions for words will be searched
     *
     * @return int word_id - processed word_id
     */
    public function processSingleWord(string $word, bool $getWord = true, bool $addRelated = true, bool $addSynonyms = true, bool $checkDefinition = true) {
        $this->_writeLog('Start processing word:' . $word );
        $sql = "SELECT word, id FROM words WHERE word = ?";
        $wordQuery = $this->db->prepare($sql);
        $wordQuery->bind_param('s', $word);
        $wordQuery->execute();
        $result = $wordQuery->get_result();

        if ($result->num_rows < 1) {
            $this->_writeLog('Word doesn\'t exist');
            if ($getWord) {
                $this->_writeLog('Get word from wordnik');
                $newWord = $this->sendRequest('https://api.wordnik.com/v4/word.json/' . $word . '?useCanonical=false&includeSuggestions=true&api_key=' . $this->wordnikApi);
                $newWord = json_decode($newWord, true);
                $word = $newWord['word'];
            }
            $word_id = $this->addNewWord($word);
        } else {
            $this->_writeLog('Word exists in db');
            $row = $result->fetch_assoc();
            $word_id = $row['id'];
        }

        $this->_writeLog('Check hyphens');
        $this->setWordHypenation($word_id, $word);

        $this->_writeLog('Check word examples');
        $this->addWordExamples($word_id, $word);

        if ($addRelated) {
            $this->_writeLog('Add related words');
            $this->_writeLog('Start time ' . time());
            $this->addRelatedWords($word_id, $word);
            $this->_writeLog('End time');
        }

        if ($checkDefinition) {
            $this->_writeLog('Check definitions for word:' . $word);
            $this->_writeLog('Start time ' . time());
            $harperDefQuery = $this->db->query("SELECT id FROM definition_instances WHERE word_id = " . $word_id . " AND source_id = 6");

            if (!is_a( $harperDefQuery, 'mysqli_result' ) || $harperDefQuery->num_rows < 6  ) {
                $this->addDefinition($word, $word_id);
            } else {
                $this->_writeLog('Definition exists, skip this step');
            }

            $this->_writeLog('End time ' . time());
        }
        $this->_writeLog('Word was processed ');
        $this->_writeLog('============================================');
        return  $word_id;
    }

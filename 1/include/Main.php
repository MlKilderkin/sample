<?php
    class Main {
        /**
         * Path to stored rules
         * @var string
         */
        private $_rulesFilePath = "../rules.json";

        /**
         * Returns path to rules files
         * @return string
         */
        private function _getRulesFilePath () {
            return $this->_rulesFilePath;
        }

        /**
         * Check if file with rules exists
         * @return bool|string
         */
        private function _checkRulesFile() {
            $rulesPath = $this->_getRulesFilePath();


            if (!$rulesPath || empty($rulesPath)) {
                return false;
            }

            $file = realpath(dirname(__FILE__) . '/' . $rulesPath);

            if (!file_exists($file)) {
                return false;
            }

            return $file;
        }

        /**
         * Get rules array from rules files. Return false if file empty or doesn't exist
         * @return array|mixed|false
         */
        public function getRules() {
            $file = $this->_checkRulesFile();

            $rules = file_get_contents($file);
            if (empty($rules)) {
                return [];
            }
            return json_decode($rules, true);

        }

        /**
         * Process provided rule. Add or update rule
         * @param array $rulePost
         * @return array
         */
        public function addUpdateRule(array $rulePost) {
            $response = [
                'success' => false,
                'message' => 'Please provide correct rule data. '
            ];

            /**
             * Return false if POST array is empty
             */
            if (empty($rulePost)) {
              return $response;
            }

            /**
             * Check each element for provided post. If requiered data missing end process
             */

            $validData = ['title', 'regex'];

            foreach ($validData as $value) {
                if (!array_key_exists($value, $rulePost) || empty($rulePost[$value])) {
                    $response['message'] .= ' Please provide rule ' . $value;
                    return $response;
                }
            }

            /**
             * If rule's file doesn't exist end process
             */
            if (!$this->_checkRulesFile()) {
                $response['message'] = 'Rules file doesn\'t exist';
                return $response;
            }

            $rules = $this->getRules();

            if (array_key_exists($rulePost['id'], $rules)) {
                /**
                 * Update existing rule
                 */
                $newRule = $this->_updateSingleRule($rules[$rulePost['id']], $rulePost);
                if (!$newRule) {
                    $response['message'] = 'Rule update failed. Rule id was - ' . $rulePost['id'];
                    return $response;
                }
                $rules[$rulePost['id']] = $newRule;
            } else {
                /**
                 * Add new rule to existing ones
                 */
                $newRuleId = count($rules) + 1;
                $newRule = $this->_updateSingleRule([], $rulePost);
                if (!$newRule) {
                    $response['message'] = 'Rule add failed. New rule id was - ' . $newRuleId;
                    return $response;
                }
                array_push($rules, $newRule);
            }



            /**
             * Save rules to file
             */
            $saveResult = $this->_saveRulesToFile($rules);
            if ($saveResult === false) {
                $response['message'] = 'Unable save to file';
            }
            $response['success'] = true;
            $response['message'] = 'Rule was saved successfully';
            return $response;

        }


        /**
         * Save json to file
         * @param array $rules
         * @return bool|int
         */
        private function _saveRulesToFile(array $rules) {
            $rules = array_values($rules);
            return file_put_contents($this->_checkRulesFile(), json_encode($rules));
        }

        /**
         * Update single rule data
         * @param array $rule
         * @param array $rulePost
         * @return array|bool
         */
        private function _updateSingleRule(array $rule = [], array $rulePost) {
            if (empty($rulePost)) {
                return false;
            }
            $rule['title'] = $rulePost['title'];
            $rule['regex'] = $rulePost['regex'];
            $rule['flag'] = $rulePost['flag'];
            $rule['datetime'] = date('Y-m-d H:i:s', time());
            return $rule;
        }

        /**
         * Delete rule from rules list
         * @param array $rulePost
         * @return array
         */
        public function deleteRule(array $rulePost) {
            $response = [
                'success' => false,
                'message' => 'Can\'t delete requested rule'
            ];
            if (empty($rulePost)) {
                return $response;
            }

            if (!$this->_checkRulesFile()) {
                $response['message'] = 'Rules file doesn\'t exist';
                return $response;
            }

            $rules = $this->getRules();

            if (!empty($rules) && array_key_exists($rulePost['id'], $rules)) {
                unset($rules[$rulePost['id']]);
                $saveResult = $this->_saveRulesToFile($rules);
                if ($saveResult === false) {
                    $response['message'] = 'Unable save to file';
                }
                $response['success'] = true;
                $response['message'] = 'Rule was deleted successfully';
            }

            return $response;
        }

        /**
         * Find strings in text by provided filters
         * @param array $postBody
         * @return array
         */
        public function checkText(array $postBody) {
            $response = [
                'success' => false,
                'message' => '',
                'matches' => [],
                'text' => $postBody['text'],
                'cleaned' => ''
            ];
            if(empty($postBody['filters'])) {
                $response['message'] = "Please provide at least one filter";
                return $response;
            }

            $rules = $this->getRules();

            if (empty($rules)) {
                $response['message'] = "Please provide rule";
                return $response;
            }
            $matches = [];
            foreach ($postBody['filters'] as $id => $filter) {

                // Check with preg_match_all do we have entities in our text
                preg_match_all('/' . $rules[$id]['regex'] . '/' . $rules[$id]['flag'], $postBody['text'], $match);
                $matches[$rules[$id]['title']] = $match;

                // Clean text
                $response['cleaned'] = preg_replace('/' . $rules[$id]['regex'] . '/' . $rules[$id]['flag'], '', $postBody['text']);

                // Update initial text
                $postBody['text'] = $response['cleaned'];
            }
            $response['matches'] = $matches;
            $response['success'] = true;
            $response['message'] = 'Text checked';
            return $response;
        }

    }

?>
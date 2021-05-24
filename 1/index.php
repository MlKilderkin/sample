<?php require_once "include/Main.php";
 $mainClass = new Main();
 $rules = $mainClass->getRules();
 if (isset($_POST['submit-text-check'])) {
    if (empty($_POST['initial-text']) || !isset($_POST['rules'])) {
        $response = ['success' => false, 'message' => 'Please provide text and filters'];
        setcookie('response', json_encode($response));
        header("Location: /index.php" );
    }
    $postBody = [
        'text' => $_POST['initial-text'],
        'filters' => $_POST['rules']
    ];
    $response = $mainClass->checkText($postBody);
     setcookie('response', json_encode($response));
     header("Location: /" );
 }

?>
<?php require_once("templates/header.php"); ?>

    <h2>
        Text Check
    </h2>
    <hr />
    <p>
        Paste text in textarea below. Then select filters and click on start button. All matched items will be shown below
    </p>
    <?php if (isset($response)) : ?>
        <div class="alert alert-<?php echo !$response['success'] ? 'danger' : 'success'?>" role="alert">
            <?php echo (!empty($response['message'])) ? $response['message'] : '';?>
        </div>
    <?php endif;?>
    <form action="" method="post">
        <div class="form-group">
            <label for="initial-text">Please paste text </label>
            <textarea name="initial-text" id="initial-text" class="form-control" rows="3"><?php echo !empty($response['text']) ? $response['text'] : ''?></textarea>
        </div>
        <?php if (!empty($rules)) :?>
            <label>Please select filters(you can drag filter in case you want change order)</label>
            <ul class="form-group filters">

                <?php foreach ($rules as $id => $rule) :?>
                <li class="form-check">
                    <input type="checkbox" class="form-check-input" value="<?php echo $id?>" name="rules[<?php echo $id?>]" />
                    <label class="form-check-label" ><?php echo $rule['title'];?></label>
                </li>
                <?php endforeach;?>
            </ul>
            <div class="form-group">
                <input type="submit" name="submit-text-check" class="btn btn-primary" value="Start" />
            </div>

        <?php else :?>
            <div class="alert alert-danger" role="alert">
                Rules files is empty or doesn't exist. Please add rules on <a href="/rules.php">rules page</a>
            </div>
        <?php endif; ?>
    </form>
    <?php if(!empty($response['matches'])): ?>
        <div class="form-group">
            <label for="text-check-result"><strong>Result:</strong></label>
            <div class="">
                <?php foreach ($response['matches'] as $title => $match) :?>
                    <h6>Matches by: <strong><?php echo $title?></strong></h6>

                    <?php foreach ($match as $key => $item): ?>
                        <?php echo implode(', ', $item);?>
                     <?php endforeach;?>
                    <hr />
                <?php endforeach;?>
            </div>
            <label for="text-check-result"><strong>Cleaned Text:</strong></label>
            <div class="">
                <?php echo $response['cleaned']; ?>
            </div>
    <?php endif;?>
<?php require_once("templates/footer.php");
<?php require_once "include/Main.php"; ?>

<?php
    $mainClass = new Main();
    $rules = $mainClass->getRules();

    if (isset($_POST['save-new-rule']) || isset($_POST['edit-rule'])) {

        $ruleToSave = [
            'id' => isset($_POST['rule-id']) && is_numeric($_POST['rule-id']) ? (int)$_POST['rule-id'] : '',
            'title' => !empty($_POST['rule-title']) ? htmlspecialchars($_POST['rule-title']) : '',
            'regex' => !empty($_POST['rule-regex']) ? $_POST['rule-regex'] : '',
            'flag' => !empty($_POST['rule-flag']) ? $_POST['rule-flag'] : '',
        ];
        $response = $mainClass->addUpdateRule($ruleToSave);
        setcookie('response', json_encode($response));
        header("Location: /rules.php" );
    }
    if (isset($_POST['delete-rule'])) {
        $ruleToDelete = [
            'id' => !empty($_POST['rule-id']) && is_numeric($_POST['rule-id']) ? $_POST['rule-id'] : '',
        ];
        $response = $mainClass->deleteRule($ruleToDelete);
        setcookie('response', json_encode($response));
        header("Location: /rules.php" );
    }
?>

<?php require_once("templates/header.php");?>

    <h2>
        Rules
    </h2>
    <hr />

    <?php if (empty($rules)) : ?>
        <div class="alert alert-primary" role="alert">
            Please add at least 1 rule before you proceed
        </div>
    <?php endif; ?>

    <?php if (isset($response)) : ?>
        <div class="alert alert-<?php echo !$response['success'] ? 'danger' : 'success'?>" role="alert">
            <?php echo (!empty($response['message'])) ? $response['message'] : '';?>
        </div>
    <?php endif;?>

    <form method="post" id="add-edit-rule"  action="/rules.php">
        <input type="hidden" name="rule-id" value="" />
        <div class="form-group">
            <input type="text" placeholder="Input rule title" class="rule-title text form-control" required name="rule-title" />
        </div>
        <div class="form-row mb-3">
            <div class="col-10 input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">/</span>
                </div>
                <input type="text" placeholder="Input php regex or string here" class="rule-regex text form-control" required name="rule-regex" />

                <div class="input-group-append">
                    <span class="input-group-text">/</span>
                </div>
            </div>
            <div class="col-2">
                <input type="text" placeholder="Type flag there" class="form-control" name="rule-flag">
            </div>
            <small>Flag 'g' should be omitted</small>
        </div>
        <div class="form-group">
            <input type="submit" value="Save rule" name="save-new-rule" class="btn btn-success">
        </div>
    </form>

    <hr />

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <th scope="col" class="text-center">#</th>
            <th scope="col" class="text-center">Title</th>
            <th scope="col" class="text-center">Date</th>
            <th scope="col" class="text-center">Action</th>
        </thead>
        <tbody>
            <?php if (empty($rules)) :?>
                <tr>
                    <td scope="row" class="text-center" colspan="3" >Rule's list is empty</td>
                </tr>
            <?php else : ?>
                <?php foreach ($rules as $id => $rule) :?>
                    <tr>
                        <td scope="row" class="text-center"><?php echo $id+1 ?></td>
                        <td scope="row" class="text-center"><?php echo $rule['title'] ?></td>
                        <td scope="row" class="text-center"><?php echo $rule['datetime'] ?></td>
                        <td scope="row" class="text-center">
                            <form action="/rules.php" method="post" data-rule="<?php echo htmlspecialchars(json_encode($rule));?>">
                                <input type="hidden" name="rule-id" value="<?php echo $id;?>" />
                                <input type="submit" value="Edit" name="edit-rule" class="btn btn-warning" />
                                <input type="submit" value="Delete" name="delete-rule" class="btn btn-danger" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>
        </tbody>
    </table>

<?php require_once("templates/footer.php");?>
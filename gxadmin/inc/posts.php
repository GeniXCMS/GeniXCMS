<?php
    if (isset($data['alertgreen'])) {
        # code...
        echo "<div class=\"alert alert-success\" >
        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">Close</span>
        </button>";
        foreach ($data['alertgreen'] as $alert) {
            # code...
            echo "$alert\n";
        }
        echo "</div>";
    }
    if (isset($data['alertred'])) {
        # code...
        echo "<div class=\"alert alert-danger\" >
        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">Close</span>
        </button>";
        foreach ($data['alertred'] as $alert) {
            # code...
            echo "$alert\n";
        }
        echo "</div>";
    }
?>
<div class="row">
    <div class="col-md-12">
        <h1>Posts <a href="index.php?page=posts&act=add" class="btn btn-primary pull-right">Add New</a></h1>
    </div>
    <div class="col-sm-12">
    <div class="row">
    <div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>Edit/Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                //print_r($data);
                if($data['num'] > 0){
                    foreach ($data['posts'] as $p) {
                        # code...
                        //echo $p->id;
                        echo "
                        <tr>
                            <td>{$p->id}</td>
                            <td><a href=\"".Url::post($p->id)."\" target=\"_new\">{$p->title}</a></td>
                            <td>".Categories::name($p->cat)."</td>
                            <td>{$p->date}</td>
                            <td>
                                <a href=\"index.php?page=posts&act=edit&id={$p->id}\" class=\"label label-success\">Edit</a> 
                                <a href=\"index.php?page=posts&act=del&id={$p->id}\" class=\"label label-danger\" 
                                onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete</a>
                            </td>
                        </tr>
                        ";
                    }
                }else{
                    echo "
                    <tr>
                        <td>No Post Found
                        </td>
                    </tr>";
                }
            ?>
            
        </tbody>
    </table>
    </div>
    </div>
    </div>
</div>
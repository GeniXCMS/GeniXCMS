<h3><i class="fa fa-database text-danger"></i> Database Error</h3>
Something went wrong with the database.<br />
<div class="alert alert-danger">
<?php
if (is_array($val)) {
    foreach ($val as $k => $v) {
        if (is_array($val[$k])) {
            echo '<ul class="list-unstyled">';
            for ($i = 0; $i < count($val[$k]); ++$i) {
                echo '<li>'.$val[$k][$i].'</li>';
            }
            echo '</ul>';
        } else {
            echo $val[$k];
        }
    }
} else {
    echo $val;
}

?>
</div>
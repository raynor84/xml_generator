<?php
foreach($this->_['entries'] as $entry){
    if ($entry == "." or $entry == "..") {
        continue;
    }
    ?>

<div id="entries"><a href="index.php?view=file_view&filename=<?php echo $entry; ?>"><?php echo $entry; ?></a></div>

<?php
}
?>

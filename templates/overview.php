<?php
foreach($this->_['entries'] as $entry){
?>
    <div id="entries"><a href="index.php?view=file_view&filename=<?php echo $entry; ?>"><?php echo $entry; ?></a></div>
<?php
}
?>

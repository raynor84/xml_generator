    <h2><?php echo $this->_['title']; ?></h2>
    <a href="?view=overview">Zur&uuml;ck zur &Uuml;bersicht</a>
    <p>
            <?php 

            require_once './templates/recursivetemplates/view_xmlfiles.php';
            readxml($this->_['file']);

            ?>
    </p>
    <hr />
    <p>
            <?php echo print_r($this->_['file']); ?>
    </p>
    <a href="?view=overview">Zur&uuml;ck zur &Uuml;bersicht</a>

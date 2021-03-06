<?php
class Controller{

	private $request = null;
	private $template = '';
	private $view = null;

	/**
	 * Konstruktor, erstellet den Controller.
	 *
	 * @param Array $request Array aus $_GET & $_POST.
	 */
	public function __construct($request){
            
                //Fetch Request and Determine View
		$this->view = new View();
		$this->request = $request;
		$this->template = !empty($request['view']) ? $request['view'] : 'overview';
	}

	/**
	 * Methode zum anzeigen des Contents.
	 *
	 * @return String Content der Applikation.
	 */
	public function display(){
		$view = new View();
		switch($this->template){
			case 'file_view':
				$filename = $this->request['filename'];
				$xmlview = XMLFileModel::readxml($filename);
				//Generate View
				$view->setTemplate('file_view');
				$view->assign('title', $filename);
                                $view->assign('file', $filename);
                                $view->assign('xmlview', $xmlview);
				break;
			
			case 'overview':
			default:
                            $entries = XMLFileModel::getXMLfiles();

                            //Generate View
                            $view->setTemplate('overview');
                            $view->assign('entries', $entries);
                            break;
		}
                
                
                //Standard Output
                $this->view->setTemplate('basic_template');
                $this->view->assign('blog_title', 'Startseite');
                $this->view->assign('blog_content', $view->loadTemplate());
                $this->view->assign('blog_footer', 'XML-Generator');
                return $this->view->loadTemplate();
	}
}
?>
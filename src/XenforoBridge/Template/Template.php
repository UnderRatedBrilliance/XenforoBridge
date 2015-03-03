<?php namespace XenforoBridge\Template;

use XenforoBridge\Contracts\TemplateInterface;
use XenForo_Application;
use XenForo_Dependencies_Public;
use XenForo_ViewRenderer_HtmlPublic;
use Zend_Controller_Request_Http;
use Zend_Controller_Response_Http;
use XenForo_Template_Abstract;



class Template implements TemplateInterface
{
	protected $xenBasePath;

	public function __construct($xenBasePath)
	{
		if(!is_string($xenBasePath))
		{
			throw new Exception('Require string passed '.gettype($xenBasePath));
		}
		$this->xenBasePath = $xenBasePath;
	}

	/**
	* Render view with Xenforo Template 
	*
	* @param string $name - template name
	* @param string $contents - xenforo template contents
	* @param array $params - overrided xenforo template parameters
	*/
	public function renderTemplate($name,$content = '', $params = array())
	{
		$template = new XenForo_Dependencies_Public();
		$template->preLoadData();
		$template->preRenderView();

		$finalParams = $this->createParams($content, $params);

		$template = $template->createTemplateObject($name,$finalParams);


		if(!$template)
		{
			return '';
		}
        $rendered = $template->render();
		return $this->replaceRequiredExternalPlaceholders($template,$rendered);
	}

    public function replaceRequiredExternalPlaceholders(XenForo_Template_Abstract $template, $rendered)
    {
        return strtr($rendered, array
        (
            '<!--XenForo_Require:JS-->'             => $template->getRequiredExternalsAsHtml('js'),
            '<!--XenForo_Require:CSS-->'            => $template->getRequiredExternalsAsHtml('css'),
            '{/*<!--XenForo_Required_Scripts-->*/}' => $template->getRequiredExternalsAsJson(),
        ));
    }

	/**
	* Get Xenforo Template Dependencies 
	*
	* @return array
	*/
	public function getDependenciesPublic()
	{

		//Initiallize Application 
		$application = new XenForo_Application();
		$dependencies = new XenForo_Dependencies_Public();
		$request = new Zend_Controller_Request_Http();

		//Set Xenforo Base Path
        $basePath = parse_url($this->xenBasePath,PHP_URL_PATH);
        $request = $request->setBasePath($basePath);
		
		$application->set('requestPaths',$application::getRequestPaths($request));

		$dependencies->preLoadData();
		
		$params = $dependencies->getEffectiveContainerParams(array(), $request);

		return $params;
	}

	/**
	* Create parameters for rendering xenforo templates
	*
	* @param string $contents - main content area of template
	* @param array $additionalParams(Optional) - merge additional parameters
	* @return array
	*/
	public function createParams($content = '', $additionalParams = array())
	{
		//Validates content
		if(!is_string((string)$content))
		{
			$content = '';
		}
		$fixed_params = array(
				'contents'    => (string)$content,
				'requestPaths'=> array('fullBasePath'=> $this->xenBasePath),
			 	'serverTimeInfo' => array('now'=> time(),'today'=>time(),'todayDow'=>time()),
                'templateTitle' => 'ItemHub',
			 	);
		
		$new_params = $this->getDependenciesPublic();

		$new_params = array_merge_recursive($new_params,$fixed_params,$additionalParams);

		return $new_params;
	}



    public function renderView($name,$content = '', $params = array())
    {
        $response = new Zend_Controller_Response_Http();
        $request = $this->getXenforoRequest();
        $dependencies = $this->getPublicDependencies();

        $view = new XenForo_ViewRenderer_HtmlPublic($dependencies,$response,$request);

        $params = $this->createParams($content, $params);

        //$viewObject =  $view->renderView('XenForo_ViewPublic_Page_View',$params,'PAGE_CONTAINER');
        //$params['contents'] = null;
        //ddp($params);
        echo 'final Render';
        $view->preloadTemplate('PAGE_CONTAINER');

        //ddp($view->renderContainer($content,$params));
        return $view->renderContainer($content,$params);
    }

    public function getPublicDependencies()
    {
        $dependencies = new XenForo_Dependencies_Public();
        $dependencies->preLoadData();

        return $dependencies;
    }

    public function getXenforoRequest()
    {
        $application = new XenForo_Application();
        $request = new Zend_Controller_Request_Http();

        //Set Xenforo Base Path
        $basePath = parse_url($this->xenBasePath,PHP_URL_PATH);
        $request = $request->setBasePath($basePath);

        $application->set('requestPaths',$application::getRequestPaths($request));

        return $request;
    }
}

<?php


class FivecentsCDNFilter
{
  public $baseUrl = null;
  public $cdnUrl = null;
  public $excludedPhrases = null;
  public $directories = null;
  public $disableForAdmin = null;

  function __construct($baseUrl, $cdnUrl, $directories, $excludedPhrases, $disableForAdmin) 
  {
		$this->baseUrl = $baseUrl;
		$this->cdnUrl = $cdnUrl;
		$this->disableForAdmin = $disableForAdmin;

		// Prepare the excludes
		$this->excludedPhrases = [];
		if(trim($excludedPhrases) != '')
		{
			$this->excludedPhrases = explode(',', $excludedPhrases);
			$this->excludedPhrases = array_map('trim', $this->excludedPhrases);
		}
		array_push($this->excludedPhrases, "]");
		array_push($this->excludedPhrases, "(");
		
		// Validate the directories
		if (trim($directories) == '') 
		{
			$directories = FIVECENTSCDN_DEFAULT_DIRECTORIES;
		}
		// Create the array
		$directoryArray = explode(',', $directories);
		if(count($directoryArray) > 0)
		{
			$directoryArray = array_map('trim', $directoryArray);
			$directoryArray = array_map('quotemeta', $directoryArray);
			$directoryArray = array_filter($directoryArray);
		}
		$this->directories = $directoryArray;
  }

  protected function rewriteUrl($asset)
  {
		$foundUrl = $asset[0];
		if(is_admin_bar_showing() && $this->disableForAdmin) {
		  return $asset[0];
		}
		foreach($this->excludedPhrases as $exclude) {
			if($exclude == '')
			  continue;

			if(stristr($foundUrl, $exclude) != false)
			  return $foundUrl;
		}

		// if (strstr($foundUrl, $this->baseUrl)) {
		// 	return str_replace($this->baseUrl, $this->cdnUrl, $foundUrl);
		// }

		$baseUrlNoProtocol = preg_replace('#^https?://#', '', $this->baseUrl);
		if (strstr($foundUrl, $baseUrlNoProtocol)) {
			return preg_replace('#https?://' . preg_quote($baseUrlNoProtocol, '#') . '#', $this->cdnUrl, $foundUrl);
		}

	  return $this->cdnUrl . $foundUrl;
  }

	protected function rewrite($html)
	{
		$directoriesRegex = implode('|', $this->directories);

		// $regex = '#(?<=[(\"\'])(?:'. quotemeta($this->baseUrl) .')?/(?:((?:'.$directoriesRegex.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

		$baseUrlNoProtocol = preg_replace('#^https?://#', '', $this->baseUrl);
		$regex = '#(?<=[(\"\'])(?:https?://'. quotemeta($baseUrlNoProtocol) .')?/(?:((?:'.$directoriesRegex.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
		
		return preg_replace_callback($regex, array(&$this, "rewriteUrl"), $html);
	}
	
	public function startRewrite()
	{
		ob_start(array($this,'rewrite'));
	}
}

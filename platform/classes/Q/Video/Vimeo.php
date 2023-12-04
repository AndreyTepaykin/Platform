<?php
require_once Q_PLUGIN_DIR.'/vendor/autoload.php';
use Vimeo\Vimeo;
use Vimeo\Exceptions\VimeoUploadException;

/**
 * @module Q
 */
/**
 * @class Q_Video_Vimeo
 */
class Q_Video_Vimeo extends Q_Video {

	function __construct () {
		if (!class_exists("Vimeo\Vimeo")) {
			throw new Exception("Vimeo PHP SDK not installed!");
		}
		$this->clientId = Q_Config::expect("Q", "video", "cloud", "upload", "vimeo", "clientId");
		$this->clientSecret = Q_Config::expect("Q", "video", "cloud", "upload", "vimeo", "clientSecret");
		$this->accessToken = Q_Config::expect("Q", "video", "cloud", "upload", "vimeo", "accessToken");
	}

	/**
	 * Create a video resource on the cloud provider
	 * @method doCreate
	 * @param {array} $params
	 * @throws {Q_Exception_MethodNotSupported|Q_Exception_Upload}
	 * @return {array} the response from the server, may contain errors
	 */
	function doCreate($params = array())
	{
		$vimeo = new Vimeo($this->clientId, $this->clientSecret, $this->accessToken);

		// Ignore any specified upload approach and size.
		$options = [
			'upload' => [
				'approach' => 'tus',
				'size' => (int)$params['size']
			],
			'name' => Q::ifset($params, 'name', "Untitled"),
			'privacy' => [
				//'download' => false, // need to upgrade vimeo account to PRO
				//'embed' => "whitelist" // need to upgrade vimeo account to PRO
			]
		];

		// Use JSON filtering so we only receive the data that we need to make an upload happen.
		$uri = '/me/videos?fields=uri,upload';

		$intent = $vimeo->request($uri, $options, 'POST');
		if ($intent['status'] !== 200) {
			$intent_error = !empty($intent['body']['error']) ? ' [' . $intent['body']['error'] . ']' : '';
			throw new VimeoUploadException('Unable to initiate an upload.' . $intent_error);
		}

		return $intent;
	}

	/**
	 * Delete video from the cloud provider
	 * @method doDelete
	 * @param {string} $videoId
	 * @throws {Q_Exception_MethodNotSupported|Q_Exception_Upload}
	 * @return {array} the response from the server, may contain errors
	 */
	function doDelete($videoId)
	{
		$vimeo = new Vimeo($this->clientId, $this->clientSecret, $this->accessToken);

		$intent = $vimeo->request('/videos/'.$videoId, [], 'DELETE');
		if ($intent['status'] >= 400) {
			$intent_error = !empty($intent['body']['error']) ? ' [' . $intent['body']['error'] . ']' : '';
			throw new Exception('Unable to delete.' . $intent_error);
		}

		return $intent;
	}

	/**
	 * Upload file to Vimeo
	 * @method upload
	 * @param {string} $filename Filename of the file to upload
	 * @param {array} [$params] The parameters to send
	 * @throws {Q_Exception_MethodNotSupported|Q_Exception_Upload}
	 * @return {array}
	 */
	function doUpload($filename, $params = array())
	{
        throw new Q_Exception_MethodNotSupported(); // make clients upload, for now
        // otherwise Q_Utils::request() with method: PATCH from PHP etc.
        // followed by method: HEAD to verify it,
        // if we ever wanted to upload a file from server to Vimeo
	}
};
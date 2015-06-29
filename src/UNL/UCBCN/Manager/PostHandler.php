<?php
namespace UNL\UCBCN\Manager;

class PostHandler
{
    const NOTICE_LEVEL_SUCCESS = 'success';
    const NOTICE_LEVEL_INFO = 'info';
    const NOTICE_LEVEL_ERROR = 'failure';

    public function handlePost(array $get, array $post, array $files)
    {
    	return Controller::$url;
    }

    public function flashNotice($level, $header, $message)
    {
    	$_SESSION['flash_notice'] = array(
    		'level' => $level,
    		'header' => $header,
    		'messageHTML' => $message
    	);
    }
}
<?php
namespace UNL\UCBCN\Manager;

interface PostHandlerInterface
{
    public function handlePost(array $get, array $post, array $files);
}
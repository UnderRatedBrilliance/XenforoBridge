<?php namespace XenforoBridge\Contracts;

interface TemplateInterface
{

    public function renderTemplate($name, $content, $params, $container);
}
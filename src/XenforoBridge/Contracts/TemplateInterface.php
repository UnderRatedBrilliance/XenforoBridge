<?php namespace Urb\XenforoBridge\Contracts;

interface TemplateInterface
{

    public function renderTemplate($name, $content, $params, $container);
}
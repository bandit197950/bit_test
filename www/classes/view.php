<?php

namespace bit_test\www\classes;

class View
{
    private $template_dir;
    private $template_name;
    private $data;

    public function __construct($template_name)
    {
        $this->template_dir = "templates";
        $this->template_name = $template_name;
    }

    public function SetData($data)
    {
        $this->data = $data;
    }

    public function Display()
    {
        if ($this->data) {
            foreach ($this->data as $key => $value) {
                if (!is_array($value)) {
                    $value = htmlspecialchars($value);
                }
                $$key = $value;
            }
        }

        include_once $this->template_dir . '/' . $this->template_name . '.tpl';
    }
}

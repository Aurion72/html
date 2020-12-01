<?php

namespace Collective\Html;

use Illuminate\Support\ViewErrorBag;

class FormBuilder extends FormBuilderBase
{
    /**
     * Create a form label element.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @param  bool $escape_html
     *
     * @return string
     */
    public function label($name, $value = null, $options = [], $escape_html = true)
    {
        if (array_key_exists('error-for', $options)) {
            $html = $this->generateErrorHtml('label', $name, $options);
            unset($options['error-for']);
        } else {
            $html = '';
        }

        return parent::label($name, $value, $options, $escape_html).' '.$html;
    }

    /**
     * @param string $type
     * @param string $name
     * @param null $value
     * @param array $options
     * @return \Illuminate\Support\HtmlString|string
     */
    public function input($type, $name, $value = null, $options = [])
    {
        
        
        $html = $this->generateErrorHtml($type, $name, $options);

        return parent::input($type, $name, $value, $options).' '.$html;
    }

    /**
     * Create a select box field.
     *
     * @param  string $name
     * @param  array $list
     * @param  string|bool $selected
     * @param  array $selectAttributes
     * @param  array $optionsAttributes
     * @param  array $optgroupsAttributes
     *
     * @return string
     */
    public function select(
        $name,
        $list = [],
        $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
        array $optgroupsAttributes = []
    ) {

	$html = $this->generateErrorHtml('select', $name, $selectAttributes);
        return parent::select($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes).' '.$html;
    
    }

    /**
     * Create a textarea input field.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     *
     * @return string
     */
    public function textarea($name, $value = null, $options = [])
    {
        $html = $this->generateErrorHtml('textarea', $name, $options);

        return parent::textarea($name, $value, $options).' '.$html;
    }

    /**
     * @param $type
     * @param $name
     * @param $options
     * @return null|string
     */
    protected function generateErrorHtml($type, $name, &$options)
    {
        $errors = session('errors');
        
        
        $error_html = null;

        if ($errors) {
            $index = $name;
            
            if($options['error-index'] ?? false){
                $index = $options['error-index'];
            }

            if ($type === 'label') $name = $options['error-for'];

            $errorBag = $options['errorBag'] ?? 'default';
            if($errors instanceof ViewErrorBag){
                $errorBag = $errors->getBag($errorBag);
            }else{
                $errorBag = $errors;
            }
            $messages = $errorBag->getMessages();
            if (isset($messages[$index])) {

                if ($type !== 'radio' && $type !== 'checkbox') {
                    $error_html = '<div class="invalid-feedback">';

                    foreach ($messages[$index] as $message) {
                        if (array_key_exists('short-feedback', $options)) {
                            $message = 'Requis';
                            unset($options['short-feedback']);
                        }
                        $error_html .= "<li>$message</li>";
                    }
                }
                if ($type !== 'label') {
                    if (array_key_exists('class', $options)) {
                        $options['class'] .= ' is-invalid';
                    } else {
                        $options['class'] = 'is-invalid';
                    }
                }

                if ($type !== 'radio' && $type !== 'checkbox') $error_html .= '</div>';
            }
        }

        return $error_html;
    }
}

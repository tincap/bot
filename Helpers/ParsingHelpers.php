<?php

namespace tincap\Bot\helpers;

use Sunra\PhpSimple\HtmlDomParser;

class ParsingHelpers
{
    /**
     * Возвращает информацию и данные form на странице
     *
     * @param $html
     * @param int $formNumber
     * @param array $beforehandData - заранее заданные параметры
     * @return array|boolean
     */
    public static function getFormData($html, $formNumber = 0, $beforehandData = [])
    {
        $dom = HtmlDomParser::str_get_html($html);
        $dom->load($html);

        $form = $dom->find('form', $formNumber);

        if (!isset($form)) {
            return null;
        }

        $action = htmlspecialchars_decode($form->action);

        $inputs = $form->find('input');

        $data = [];

        foreach ($inputs as $input) {
            $data[$input->name] = html_entity_decode($input->value);
        }

        $select = $form->find('select');

        foreach ($select as $selectItem) {
            $data[$selectItem->name] = null;
        }

        $buttons = $form->find('button');

        foreach ($buttons as $button) {
            $data[$button->name] = html_entity_decode($button->value);
        }

        foreach ($beforehandData as $key => $value) {
            $data[$key] = $value;
        }

        return [
            'action' => $action,
            'data' => $data,
        ];
    }
}
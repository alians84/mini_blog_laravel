<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest {
    /**
     * Определяет, есть ли права у пользователя на этот запрос
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Возвращает массив правил для проверки полей формы
     *
     * @return array
     */
    public function rules() {
        return [
            'title' => 'required|unique:posts|min:3|max:100',
            'excerpt' => 'required|min:100|max:200',
            'body' => 'required',
            'image' => 'mimes:jpeg,bmp,png|max:5000'
        ];
    }

    /**
     * Возвращает массив сообщений об ошибках для заданных правил
     *
     * @return array
     */
    public function messages() {
        return [
            'required' => 'Поле «:attribute» обязательно для заполнения',
            'unique' => 'Такое значение поля «:attribute» уже используется',
            'min' => [
                'string' => 'Поле «:attribute» должно быть не меньше :min символов',
                'file' => 'Файл «:attribute» должен быть не меньше :min Кбайт'
            ],
            'max' => [
                'string' => 'Поле «:attribute» должно быть не больше :max символов',
                'file' => 'Файл «:attribute» должен быть не больше :max Кбайт'
            ],
            'mimes' => 'Файл «:attribute» должен иметь формат :values',
        ];
    }

    /**
     * Возвращает массив дружественных пользователю названий полей
     *
     * @return array
     */
    public function attributes() {
        return [
            'title' => 'Заголовок',
            'excerpt' => 'Анонс поста',
            'body' => 'Текст поста',
            'image' => 'Изображение',
        ];
    }
}

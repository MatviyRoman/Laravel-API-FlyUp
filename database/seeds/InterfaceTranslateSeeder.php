<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterfaceTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $data = [
		    [
			    'interface_entity_id' => '1',
			    'language_id' => '1',
			    'value' => 'Блог'
		    ],
		    [
			    'interface_entity_id' => '2',
			    'language_id' => '1',
			    'value' => 'Войти'
		    ],
		    [
			    'interface_entity_id' => '3',
			    'language_id' => '1',
			    'value' => 'Услуги'
		    ],
		    [
			    'interface_entity_id' => '4',
			    'language_id' => '1',
			    'value' => 'Контакты'
		    ],
		    [
			    'interface_entity_id' => '5',
			    'language_id' => '1',
			    'value' => 'Портфолио'
		    ],
		    [
			    'interface_entity_id' => '6',
			    'language_id' => '1',
			    'value' => 'Заказать сейчас'
		    ],
		    [
			    'interface_entity_id' => '7',
			    'language_id' => '1',
			    'value' => 'Имя'
		    ],
		    [
			    'interface_entity_id' => '8',
			    'language_id' => '1',
			    'value' => 'Email'
		    ],
		    [
			    'interface_entity_id' => '9',
			    'language_id' => '1',
			    'value' => 'Телефон'
		    ],
		    [
			    'interface_entity_id' => '10',
			    'language_id' => '1',
			    'value' => 'Сообщение'
		    ],
		    [
			    'interface_entity_id' => '11',
			    'language_id' => '1',
			    'value' => 'Отправить сообщение'
		    ],
		    [
			    'interface_entity_id' => '12',
			    'language_id' => '1',
			    'value' => 'Введите Ваше имя'
		    ],
		    [
			    'interface_entity_id' => '13',
			    'language_id' => '1',
			    'value' => 'Неправильный Email'
		    ],
		    [
			    'interface_entity_id' => '14',
			    'language_id' => '1',
			    'value' => 'Неправильный номер телефона'
		    ],
		    [
			    'interface_entity_id' => '15',
			    'language_id' => '1',
			    'value' => 'Оставьте Ваше сообщение'
		    ],
		    [
			    'interface_entity_id' => '16',
			    'language_id' => '1',
			    'value' => 'Поздравление'
		    ],
		    [
			    'interface_entity_id' => '17',
			    'language_id' => '1',
			    'value' => 'Вы только что отправили сообщение'
		    ],
		    [
			    'interface_entity_id' => '18',
			    'language_id' => '1',
			    'value' => 'Главная'
		    ],
		    [
			    'interface_entity_id' => '19',
			    'language_id' => '1',
			    'value' => 'Заказать хостинг'
		    ],
		    [
			    'interface_entity_id' => '20',
			    'language_id' => '1',
			    'value' => 'Пароль'
		    ],
		    [
			    'interface_entity_id' => '21',
			    'language_id' => '1',
			    'value' => 'Войти в личный кабинет'
		    ],
		    [
			    'interface_entity_id' => '22',
			    'language_id' => '1',
			    'value' => 'Забыли пароль?'
		    ],
		    [
			    'interface_entity_id' => '23',
			    'language_id' => '1',
			    'value' => 'Восстановить'
		    ],
		    [
			    'interface_entity_id' => '24',
			    'language_id' => '1',
			    'value' => 'Неправильный Email'
		    ],
		    [
			    'interface_entity_id' => '25',
			    'language_id' => '1',
			    'value' => 'Неправильный пароль'
		    ],
		    [
			    'interface_entity_id' => '26',
			    'language_id' => '1',
			    'value' => 'Восстановить пароль'
		    ],
		    [
			    'interface_entity_id' => '27',
			    'language_id' => '1',
			    'value' => 'Повторите пароль'
		    ],
		    [
			    'interface_entity_id' => '28',
			    'language_id' => '1',
			    'value' => 'Пароль успешно сохранен'
		    ],
		    [
			    'interface_entity_id' => '29',
			    'language_id' => '1',
			    'value' => 'EUROPEAN GUARD CONSULTING OY'
		    ],
		    [
			    'interface_entity_id' => '30',
			    'language_id' => '1',
			    'value' => 'Чтобы начать сотрудничество, оставьте заявку, и мы свяжемся с вами'
		    ],
		    [
			    'interface_entity_id' => '31',
			    'language_id' => '1',
			    'value' => 'Americantie, 81, 07830 Pockar ID компании: 2806115-5'
		    ],
		    [
			    'interface_entity_id' => '32',
			    'language_id' => '1',
			    'value' => 'Леонид Качуляк'
		    ],
		    [
			    'interface_entity_id' => '33',
			    'language_id' => '1',
			    'value' => 'ВЕБ-РАЗРАБОТКА'
		    ],
		    [
			    'interface_entity_id' => '34',
			    'language_id' => '1',
			    'value' => 'Landing Page'
		    ],
		    [
			    'interface_entity_id' => '35',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '36',
			    'language_id' => '1',
			    'value' => 'Сайт-визитка'
		    ],
		    [
			    'interface_entity_id' => '37',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '38',
			    'language_id' => '1',
			    'value' => 'Интернет-магазин'
		    ],
		    [
			    'interface_entity_id' => '39',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '40',
			    'language_id' => '1',
			    'value' => 'Корпоративный, бизнес сайт'
		    ],
		    [
			    'interface_entity_id' => '41',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '42',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '43',
			    'language_id' => '1',
			    'value' => 'Индивидуальные разработки'
		    ],
		    [
			    'interface_entity_id' => '44',
			    'language_id' => '1',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '45',
			    'language_id' => '1',
			    'value' => 'Разработка сайтов'
		    ],
		    [
			    'interface_entity_id' => '46',
			    'language_id' => '1',
			    'value' => 'Страница не найдена'
		    ],
		    [
			    'interface_entity_id' => '47',
			    'language_id' => '1',
			    'value' => 'Вы указали неправильный адрес или перешли по некорректной ссылке'
		    ],
		    [
			    'interface_entity_id' => '48',
			    'language_id' => '1',
			    'value' => 'Ошибка сервера'
		    ],
		    [
			    'interface_entity_id' => '49',
			    'language_id' => '1',
			    'value' => 'При обработке запроса произошла ошибка, пожалуйста, повторите операцию позже'
		    ],
		    [
			    'interface_entity_id' => '52',
			    'language_id' => '1',
			    'value' => 'Нет результатов'
		    ],
		    [
			    'interface_entity_id' => '1',
			    'language_id' => '2',
			    'value' => 'Blog'
		    ],
		    [
			    'interface_entity_id' => '2',
			    'language_id' => '2',
			    'value' => 'Sign in'
		    ],
		    [
			    'interface_entity_id' => '3',
			    'language_id' => '2',
			    'value' => 'Services'
		    ],
		    [
			    'interface_entity_id' => '4',
			    'language_id' => '2',
			    'value' => 'Contacts'
		    ],
		    [
			    'interface_entity_id' => '6',
			    'language_id' => '2',
			    'value' => 'Order Now'
		    ],
		    [
			    'interface_entity_id' => '7',
			    'language_id' => '2',
			    'value' => 'Name'
		    ],
		    [
			    'interface_entity_id' => '8',
			    'language_id' => '2',
			    'value' => 'Email'
		    ],
		    [
			    'interface_entity_id' => '9',
			    'language_id' => '2',
			    'value' => 'Phone'
		    ],
		    [
			    'interface_entity_id' => '10',
			    'language_id' => '2',
			    'value' => 'Message'
		    ],
		    [
			    'interface_entity_id' => '11',
			    'language_id' => '2',
			    'value' => 'Send a message'
		    ],
		    [
			    'interface_entity_id' => '12',
			    'language_id' => '2',
			    'value' => 'Enter your name'
		    ],
		    [
			    'interface_entity_id' => '13',
			    'language_id' => '2',
			    'value' => 'Wrong Email'
		    ],
		    [
			    'interface_entity_id' => '14',
			    'language_id' => '2',
			    'value' => 'Invalid phone number'
		    ],
		    [
			    'interface_entity_id' => '15',
			    'language_id' => '2',
			    'value' => 'Leave your message'
		    ],
		    [
			    'interface_entity_id' => '16',
			    'language_id' => '2',
			    'value' => 'Congratulations'
		    ],
		    [
			    'interface_entity_id' => '17',
			    'language_id' => '2',
			    'value' => 'You just sent a message'
		    ],
		    [
			    'interface_entity_id' => '18',
			    'language_id' => '2',
			    'value' => 'Home'
		    ],
		    [
			    'interface_entity_id' => '19',
			    'language_id' => '2',
			    'value' => 'Order hosting'
		    ],
		    [
			    'interface_entity_id' => '20',
			    'language_id' => '2',
			    'value' => 'Password'
		    ],
		    [
			    'interface_entity_id' => '21',
			    'language_id' => '2',
			    'value' => 'Login to your account'
		    ],
		    [
			    'interface_entity_id' => '22',
			    'language_id' => '2',
			    'value' => 'Forgot your password?'
		    ],
		    [
			    'interface_entity_id' => '23',
			    'language_id' => '2',
			    'value' => 'Restore'
		    ],
		    [
			    'interface_entity_id' => '24',
			    'language_id' => '2',
			    'value' => 'Wrong Email'
		    ],
		    [
			    'interface_entity_id' => '25',
			    'language_id' => '2',
			    'value' => 'Incorrect password'
		    ],
		    [
			    'interface_entity_id' => '26',
			    'language_id' => '2',
			    'value' => 'Recover password'
		    ],
		    [
			    'interface_entity_id' => '27',
			    'language_id' => '2',
			    'value' => 'Repeat password'
		    ],
		    [
			    'interface_entity_id' => '28',
			    'language_id' => '2',
			    'value' => 'Password successfully saved'
		    ],
		    [
			    'interface_entity_id' => '29',
			    'language_id' => '2',
			    'value' => 'EUROPEAN GUARD CONSULTING OY'
		    ],
		    [
			    'interface_entity_id' => '30',
			    'language_id' => '2',
			    'value' => 'To start cooperation, please leave a request and we will contact you'
		    ],
		    [
			    'interface_entity_id' => '31',
			    'language_id' => '2',
			    'value' => 'Americantie, 81, 07830 Pockar Company ID: 2806115-5'
		    ],
		    [
			    'interface_entity_id' => '32',
			    'language_id' => '2',
			    'value' => 'Leonid Kachuliak'
		    ],
		    [
			    'interface_entity_id' => '33',
			    'language_id' => '2',
			    'value' => 'WEB-DEVELOPMENT'
		    ],
		    [
			    'interface_entity_id' => '34',
			    'language_id' => '2',
			    'value' => 'Landing Page'
		    ],
		    [
			    'interface_entity_id' => '35',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '36',
			    'language_id' => '2',
			    'value' => 'Business Card Site'
		    ],
		    [
			    'interface_entity_id' => '37',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '38',
			    'language_id' => '2',
			    'value' => 'Online Store'
		    ],
		    [
			    'interface_entity_id' => '39',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '40',
			    'language_id' => '2',
			    'value' => 'Corporate, Business Website'
		    ],
		    [
			    'interface_entity_id' => '41',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '42',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '43',
			    'language_id' => '2',
			    'value' => 'Custom Development'
		    ],
		    [
			    'interface_entity_id' => '44',
			    'language_id' => '2',
			    'value' => null
		    ],
		    [
			    'interface_entity_id' => '45',
			    'language_id' => '2',
			    'value' => 'Website development'
		    ],
		    [
			    'interface_entity_id' => '46',
			    'language_id' => '2',
			    'value' => 'Page not found'
		    ],
		    [
			    'interface_entity_id' => '47',
			    'language_id' => '2',
			    'value' => 'You entered an incorrect address or clicked on an incorrect link'
		    ],
		    [
			    'interface_entity_id' => '48',
			    'language_id' => '2',
			    'value' => 'Server error'
		    ],
		    [
			    'interface_entity_id' => '49',
			    'language_id' => '2',
			    'value' => 'An error occurred while processing the request, please try again later.'
		    ],
		    [
			    'interface_entity_id' => '50',
			    'language_id' => '1',
			    'value' => '+358 40 756 81 77'
		    ],
		    [
			    'interface_entity_id' => '51',
			    'language_id' => '1',
			    'value' => 'info@egc.fi'
		    ],
	    ];

	    DB::table('interface_translates')->insert($data);
    }
}

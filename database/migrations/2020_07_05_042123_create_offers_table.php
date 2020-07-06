<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
			$table->integer('card_id')->comment('ID оффера с сайта-источника');
			$table->string('name');
			$table->decimal('k5m', 3, 1)->comment('Рейтинг финансового продукта');
			$table->integer('amount_min')->comment('Min сумма займа');
			$table->integer('amount_max')->comment('Max сумма займа');
			$table->integer('term_min')->comment('Min срок кредитования');
			$table->integer('term_max')->comment('Max срок кредитования');
			$table->integer('age_min')->comment('Min возраст');
			$table->integer('age_max')->nullable()->comment('Max возраст');
			$table->decimal('daily_rate', 4, 2)->comment('Ставка в день');
			$table->integer('overpayment')->comment('Мин. размер переплаты');
			$table->string('logo')->nullable();
			$table->string('label')->nullable()->comment('Значок на логотипе');
			$table->integer('reviews_count')->default(0)->comment('Кол-во отзывов');
			$table->decimal('reviews_rating', 3, 2)->default(0)->comment('Оценка по отзывам');
			$table->string('reviews_icon')->nullable()->comment('Иконка со звездочками');
			$table->decimal('approval', 3, 1)->comment('Процент одобрения заявок');
			$table->integer('min_credit_rating')->comment('Min рейтинг заявителя');
			
			$table->string('option_icons')->comment('Опции с иконками');
			$table->string('payment_methods')->comment('Способы выплаты');
			$table->string('repayment_methods')->comment('Способы погашения');
			
			$table->string('docs')->comment('Документы для оформления');
			$table->string('processing_speed')->comment('Скорость рассмотрения заявки');
			$table->string('payout_speed')->comment('Скорость выплаты');
			$table->string('identification')->nullable()->comment('Идентификация');
			$table->string('schedule')->comment('График работы');
			$table->string('bad_history')->nullable()->comment('Плохая КИ');
			$table->string('prolong')->nullable()->comment('Возможность продления');
			$table->string('investors')->nullable()->comment('Условия для инвесторов');
			
			$table->text('info_list')->comment('Список под таблицей');
			$table->text('description')->comment('Подробное описание');
			$table->text('files')->nullable()->comment('Файлы для скачивания');
			
			$table->integer('year')->nullable()->comment('Год создания');
			$table->string('url')->default('#')->comment('Ссылка на оформление');
			$table->tinyInteger('is_specoffer')->default(0)->comment('Выделенное спецпредложение');
			$table->tinyInteger('sort')->default(0)->comment('Порядок сортировки (DESC)');
			$table->tinyInteger('active')->default(1)->comment('1 - оффер активный, 0 - не активный');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}

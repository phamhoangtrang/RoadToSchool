<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::defaultStringLength(191);

        \Carbon\Carbon::setLocale('vi');
        \Illuminate\Support\Carbon::setLocale('vi');

        try {
            if (Schema::hasTable('conversation_messages')) {
                // Đảm bảo cột content là TEXT (không bị giới hạn 191 ký tự)
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE conversation_messages MODIFY content TEXT');

                // Tự động xóa các tin nhắn trùng lặp giống hệt nhau liên tiếp sinh ra do lỗi lặp polling cũ
                \Illuminate\Support\Facades\DB::statement("
                    DELETE m1 FROM conversation_messages m1
                    INNER JOIN conversation_messages m2 
                        ON m1.conversation_id = m2.conversation_id 
                        AND m1.content = m2.content 
                        AND m1.id > m2.id
                        AND ABS(TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at)) <= 300
                ");
            }
        } catch (\Throwable $e) {
            // Silently skip if DB is not reachable in CLI sandbox
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

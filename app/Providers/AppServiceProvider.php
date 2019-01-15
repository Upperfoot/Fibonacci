<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dictionary\RedisSchemaDictionary;
use App\Service\Fibonacci\Formatter;
use App\Service\Fibonacci\ReaderService;
use App\Service\Fibonacci\WriterService;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 *
 * @author PB
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * @author PB
     */
    public function register(): void
    {
        $this->app->bind(RedisSchemaDictionary::class);
        $this->app->bind(WriterService::class);
        $this->app->bind(ReaderService::class);
        $this->app->bind(Formatter::class);
    }
}

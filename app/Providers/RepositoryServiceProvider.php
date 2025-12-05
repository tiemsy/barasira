<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interface\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ServiceRepositoryInterface::class,
            \App\Repositories\Eloquent\ServiceRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ServiceCategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\ServiceCategoryRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\MissionRepositoryInterface::class,
            \App\Repositories\Eloquent\MissionRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ApplicationRepositoryInterface::class,
            \App\Repositories\Eloquent\ApplicationRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\PaymentRepositoryInterface::class,
            \App\Repositories\Eloquent\PaymentRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ReviewRepositoryInterface::class,
            \App\Repositories\Eloquent\ReviewRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ResumeRepositoryInterface::class,
            \App\Repositories\Eloquent\ResumeRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ResumeLanguageRepositoryInterface::class,
            \App\Repositories\Eloquent\ResumeLanguageRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ResumeTagRepositoryInterface::class,
            \App\Repositories\Eloquent\ResumeTagRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\DocumentRepositoryInterface::class,
            \App\Repositories\Eloquent\DocumentRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\EducationRepositoryInterface::class,
            \App\Repositories\Eloquent\EducationRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\ExperienceRepositoryInterface::class,
            \App\Repositories\Eloquent\ExperienceRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\CertificationRepositoryInterface::class,
            \App\Repositories\Eloquent\CertificationRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\NotificationRepositoryInterface::class,
            \App\Repositories\Eloquent\NotificationRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\MessageRepositoryInterface::class,
            \App\Repositories\Eloquent\MessageRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\DisputeRepositoryInterface::class,
            \App\Repositories\Eloquent\DisputeRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\FavoriteRepositoryInterface::class,
            \App\Repositories\Eloquent\FavoriteRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\PortfolioItemRepositoryInterface::class,
            \App\Repositories\Eloquent\PortfolioItemRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\Interface\UserSkillRepositoryInterface::class,
            \App\Repositories\Eloquent\UserSkillRepositoryEloquent::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

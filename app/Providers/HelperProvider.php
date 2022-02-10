<?php

namespace App\Providers {

    use App\Adapters\Coin\BitcoinAdapter;
    use App\Adapters\Coin\BitcoinCashAdapter;
    use App\Adapters\Coin\BitcoinGoldAdapter;
    use App\Adapters\Coin\BitcoinSVAdapter;
    use App\Adapters\Coin\DashAdapter;
    use App\Adapters\Coin\LitecoinAdapter;
    use App\Helpers\SettingsHelper;

    use HolluwaTosin360\BitGoPHP\BitGo;

    use Illuminate\Support\Collection;
    use Illuminate\Support\ServiceProvider;

    use Laravel\Passport\Console\ClientCommand;
    use Laravel\Passport\Console\InstallCommand;
    use Laravel\Passport\Console\KeysCommand;

    class HelperProvider extends ServiceProvider
	{
		/**
		 * Register services.
		 *
		 * @return void
		 * @throws \Illuminate\Contracts\Container\BindingResolutionException
		 */
		public function register()
		{
			$this->app->bind(BitGo::class, function ($app) {
				return new BitGo(
					config()->get('bitgo.host'),
					config()->get('bitgo.port'),
					config()->get('bitgo.token')
				);
			});

			$this->app->singleton(SettingsHelper::class, function ($app) {
				return new SettingsHelper();
			});

			$this->app->singleton('CoinAdapters', function ($app) {
				return new Collection([
					BitcoinAdapter::class,
					LitecoinAdapter::class,
					DashAdapter::class,
					BitcoinCashAdapter::class,
					BitcoinGoldAdapter::class,
					BitcoinSVAdapter::class,
				]);
			});
		}



		/**
		 * Bootstrap services.
		 *
		 * @return void
		 */
		public function boot()
		{
			$this->commands(InstallCommand::class);
			$this->commands(KeysCommand::class);
			$this->commands(ClientCommand::class);
		}

	}
}

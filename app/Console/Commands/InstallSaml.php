<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallSaml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saml:install
                            {--force : Overwrite existing certificates}
                            {--domain= : Domain name for the certificate (defaults to APP_URL domain)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure SAML certificates for SURF Conext authentication';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing SAML configuration...');
        $this->newLine();

        // Create SAML directory
        $samlDir = storage_path('app/saml');
        if (!File::exists($samlDir)) {
            File::makeDirectory($samlDir, 0755, true);
            $this->info("✓ Created directory: {$samlDir}");
        } else {
            $this->line("Directory already exists: {$samlDir}");
        }

        // Generate SP certificates
        $this->generateSpCertificates($samlDir);

        // Check for SURF certificate
        $this->checkSurfCertificate($samlDir);

        // Display configuration information
        $this->displayConfigurationInfo();

        $this->newLine();
        $this->info('✓ SAML installation complete!');
        $this->newLine();
        $this->comment('Next steps:');
        $this->line('1. Configure your .env file with SURF Conext settings');
        $this->line('2. Register your application in SURF Conext');
        $this->line('3. Provide your SP metadata URL: ' . config('app.url') . '/saml/metadata');
        $this->line('4. Download SURF Conext certificate and save it to: ' . storage_path('app/saml/surf_public.crt'));

        return Command::SUCCESS;
    }

    protected function generateSpCertificates(string $samlDir): void
    {
        $privateKeyPath = $samlDir . '/sp_private.key';
        $publicCertPath = $samlDir . '/sp_public.crt';

        // Check if certificates already exist
        if (File::exists($privateKeyPath) && File::exists($publicCertPath) && !$this->option('force')) {
            $this->line('SP certificates already exist. Use --force to overwrite.');
            return;
        }

        $this->info('Generating SP certificates...');

        // Get domain from option or APP_URL
        $domain = $this->option('domain') ?: parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

        // Generate private key
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $privateKey = openssl_pkey_new($config);
        if (!$privateKey) {
            $this->error('Failed to generate private key: ' . openssl_error_string());
            return;
        }

        // Export private key
        openssl_pkey_export($privateKey, $privateKeyPem);
        File::put($privateKeyPath, $privateKeyPem);
        chmod($privateKeyPath, 0600); // Secure permissions
        $this->info("✓ Generated private key: {$privateKeyPath}");

        // Generate certificate signing request
        $dn = [
            'countryName' => 'NL',
            'stateOrProvinceName' => 'Netherlands',
            'localityName' => 'Eindhoven',
            'organizationName' => config('app.name', 'Projects Portal'),
            'organizationalUnitName' => 'IT',
            'commonName' => $domain,
            'emailAddress' => config('mail.from.address', 'admin@example.com'),
        ];

        $csr = openssl_csr_new($dn, $privateKey, $config);
        if (!$csr) {
            $this->error('Failed to generate CSR: ' . openssl_error_string());
            return;
        }

        // Generate self-signed certificate (valid for 1 year)
        $cert = openssl_csr_sign($csr, null, $privateKey, 365, $config);
        if (!$cert) {
            $this->error('Failed to generate certificate: ' . openssl_error_string());
            return;
        }

        // Export certificate
        openssl_x509_export($cert, $certPem);
        File::put($publicCertPath, $certPem);
        chmod($publicCertPath, 0644);
        $this->info("✓ Generated public certificate: {$publicCertPath}");

        // Display certificate info
        $certInfo = openssl_x509_parse($cert);
        $this->line("  Subject: {$certInfo['subject']['CN']}");
        $this->line("  Valid until: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));
    }

    protected function checkSurfCertificate(string $samlDir): void
    {
        $surfCertPath = $samlDir . '/surf_public.crt';

        if (File::exists($surfCertPath)) {
            $this->line("✓ SURF Conext certificate found: {$surfCertPath}");
        } else {
            $this->warn("⚠ SURF Conext certificate not found: {$surfCertPath}");
            $this->line('  You need to download the SURF Conext certificate and save it to this location.');
            $this->line('  Certificate URL: https://engine.surfconext.nl/authentication/idp/metadata');
        }
    }

    protected function displayConfigurationInfo(): void
    {
        $this->newLine();
        $this->info('SAML Configuration Information:');
        $this->newLine();

        $appUrl = config('app.url');
        $spEntityId = $appUrl . '/saml/metadata';
        $acsUrl = $appUrl . '/saml/acs';
        $slsUrl = $appUrl . '/saml/sls';

        $this->table(
            ['Setting', 'Value'],
            [
                ['SP Entity ID', $spEntityId],
                ['ACS URL', $acsUrl],
                ['SLS URL', $slsUrl],
                ['Metadata URL', $appUrl . '/saml/metadata'],
                ['Private Key Path', storage_path('app/saml/sp_private.key')],
                ['Public Cert Path', storage_path('app/saml/sp_public.crt')],
                ['SURF Cert Path', storage_path('app/saml/surf_public.crt')],
            ]
        );

        $this->newLine();
        $this->comment('Add these to your .env file:');
        $this->line("SAML_SP_ENTITY_ID={$spEntityId}");
        $this->line("SAML_SP_ACS_URL={$acsUrl}");
        $this->line("SAML_SP_SLS_URL={$slsUrl}");
        $this->line("SAML_SP_METADATA_URL={$appUrl}/saml/metadata");
        $this->line("SAML_SP_PRIVATE_KEY_PATH=storage/app/saml/sp_private.key");
        $this->line("SAML_SP_PUBLIC_CERT_PATH=storage/app/saml/sp_public.crt");
        $this->line("SURF_PUBLIC_CERT_PATH=storage/app/saml/surf_public.crt");
    }
}

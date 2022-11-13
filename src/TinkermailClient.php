<?php
namespace SendStreak\SendStreakPhp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;

/**
 * API Client to make requests to SendStreak.
 */
class SendStreakClient {

    private const RESPONSE_STATUS_UNAUTHORIZED = 401;

    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $apiKey, LoggerInterface $logger = null, array $options = [])
    {
        if (!$apiKey) {
            throw new \Exception("An API key is required to initialize the SendStreak SDK.");
        }

        $this->apiKey = $apiKey;
        $this->guzzleClient = new Client();
        $this->logger = $logger ?: new NullLogger();
        $this->options = $options;
    }

    /**
     * Returns the API key used by the client.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Sets the API key used by the client.
     */
    public function setApiKey(string $value): void
    {
        if (!$value) {
            throw new \Exception("A valid API key must be provided.");
        }

        $this->apiKey = $value;
    }

    /**
     * Returns the additional options set for HTTP requests.
     * These are Guzzle options. More information: https://docs.guzzlephp.org/en/stable/request-options.html
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Sets the additional options for HTTP requests.
     * These are Guzzle options. More information: https://docs.guzzlephp.org/en/stable/request-options.html
     * 
     * For advanced use only!
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Sends a single mail using the template specified by the template slug.
     * 
     * @param string $recipientAddress The email address of the recipient.
     * @param string $templateSlug The slug string of the template to use.
     * @param string $variables Variables used by the template.
     */
    public function sendMail(string $recipientAddress, string $templateSlug, array $variables = []): void
    {
        $this->invokeSendStreakApi("/v1/messages", $this->createMailPayload($recipientAddress, $templateSlug, $variables));
    }

    /**
     * Sends a single mail asynchronously using the template specified by the template slug.
     * 
     * @param string $recipientAddress The email address of the recipient.
     * @param string $templateSlug The slug string of the template to use.
     * @param string $variables Variables used by the template.
     */
    public function sendMailAsync(string $recipientAddress, string $templateSlug, array $variables = []): PromiseInterface
    {
        return $this->invokeSendStreakApiAsync("/v1/messages", $this->createMailPayload($recipientAddress, $templateSlug, $variables));
    }

    /**
     * Updates or creates a contact if it does not exist already. 
     * 
     * @param Contact $contact An object containing the contact's data.
     */
    public function updateContact(Contact $contact): void
    {
        $this->invokeSendStreakApi("/v1/contacts", $contact->jsonSerialize());
    }

    /**
     * Updates or creates a contact asynchronously if it does not exist already. 
     * 
     * @param Contact $contact An object containing the contact's data.
     */
    public function updateContactAsync(Contact $contact): PromiseInterface
    {
        return $this->invokeSendStreakApiAsync("/v1/contacts", $contact->jsonSerialize());
    }

    private function invokeSendStreakApi(string $path, array $body): void
    {
        try {
            $this->guzzleClient->request('POST', $path, $this->createClientOptions($body));
        } catch (BadResponseException $exception) {
            $this->handleBadResponse($exception);
        } catch (\Throwable $exception) {
            $this->logger->error("Error invoking the SendStreak API. Error message: {$exception->getMessage()}");
        }
    }

    private function invokeSendStreakApiAsync(string $path, array $body): PromiseInterface
    {
        return $this->guzzleClient->requestAsync('POST', $path, $this->createClientOptions($body))
            ->otherwise(function ($error) {
                if ($error instanceof BadResponseException) {
                    $this->handleBadResponse($error);
                } else {
                    $this->logger->error("Error invoking the SendStreak API. Error message: {$error->getMessage()}");
                }
            });
    }

    private function createMailPayload(string $recipientAddress, string $templateSlug, array $variables = []): array
    {
        $payload = [
            'rcpt' => $recipientAddress,
            'templateSlug' => $templateSlug, 
        ];

        if (!empty($variables)) {
            $payload['variables'] = $variables;
        }
        return $payload;
    }

    private function createClientOptions(array $body = []): array
    {
        $options = [
            'json' => $body,
        ];
        $options = array_merge_recursive($options, $this->getDefaultOptions());
        $options = array_merge_recursive($options, $this->options);
        return $options;
    }

    private function getDefaultOptions(): array
    {
        return [
            'base_uri' => "https://api.sendstreak.com",
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => "application/json"
            ],
        ];
    }

    private function handleBadResponse(BadResponseException $exception): void 
    {
        $response = $exception->getResponse();
        if ($response->getStatusCode() === self::RESPONSE_STATUS_UNAUTHORIZED) {
            $this->logger->error("Error invoking the SendStreak API. An invalid API key was provided.");
        } else {
            $this->logger->error("Error invoking the SendStreak API. Error message: {$exception->getMessage()}");
        }
    }
}

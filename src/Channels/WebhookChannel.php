<?php


namespace Siewwp\LaravelServiceHost;

use Acquia\Hmac\KeyInterface;
use RuntimeException;
use Illuminate\Notifications\Notification;
use Siewwp\HmacHttp\HttpClient;

class WebhookChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$url = $this->getWebhookUrl($notifiable, $notification)
            || $key = $this->getHmacKey($notifiable, $notification)) {
            return;
        }
        
        (new HttpClient([], $key))->request('post', $url, $this->buildJsonPayload(
            $notifiable, $notification
        ));
    }

    protected function getWebhookUrl($notifiable, $notification)
    {
        return $notifiable->routeNotificationFor('webhook_url', $notification) ?: $notifiable->webhook_url;
    }

    /**
     * @param $notifiable
     * @param $notification
     * @return KeyInterface
     */
    protected function getHmacKey($notifiable, $notification)
    {
        return $notifiable->routeNotificationFor('hmac_key', $notification);
    }
    
    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toWebhook')) {
            return $notification->toWebhook($notifiable);
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toWebhook / toArray method.');
    }

    /**
     * Build an array payload for the webhook Notification Model.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function buildJsonPayload($notifiable, Notification $notification)
    {
        return [
            'json' => [
                'type' => $this->getClassNameWithoutNamespace($notification),
                'data' => $this->getData($notifiable, $notification),
            ]
        ];
    }

    protected function getClassNameWithoutNamespace($class) {
        $path = explode('\\', get_class($class));
        return array_pop($path);
    }
}
<?php
/*
 * This file is part of the flarum extension flarum-ext-post-notification.
 *
 * (c) Timotheus Pokorra <timotheus.pokorra@solidcharity.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace RetechVN\TeleNoti\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Post\Event\Posted;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostNotification
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    protected static $flarumConfig;

    /**
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        static::$flarumConfig = app('flarum.config');
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Posted::class, [$this, 'PostWasPosted']);
    }

    public function PostWasPosted(Posted $event)
    {
        $this->SendNotification($event->post);
    }
    private function SendNotification($post)
    {
        $tag_slug = $post->discussion->tags[0]->slug;
        $title = $post->discussion->title;
        $link = Arr::get(static::$flarumConfig, 'url') . '/d/' . $post->discussion->id . '-' . $post->discussion->slug . '/' . $post->number;

        // $message =  $title . " [link](" . $link . ")";
$message = "🎉 **$title** 🎉
---
🔗 **[Nhấp vào đây để xem liên kết]($link)**
---
📅 *Cập nhật vào:* " . date('Y-m-d H:i') . "
Hãy theo dõi để nhận thêm các cập nhật mới!* ✨";




        $chatId1 = '-4559681927';
        $chatId2 = '-4534475318';

        $this->sendMessage($chatId1, $message);

        if ($tag_slug === 'tim-support') {
            $this->sendMessage($chatId2, $message);
        }
    }

    private function sendMessage($chatId, $message)
    {
        $url = "https://api.telegram.org/bot7585890162:AAEwkcpR45mN7WQ3H2iPdYVRrXcKy5bwlaU/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
    }



}

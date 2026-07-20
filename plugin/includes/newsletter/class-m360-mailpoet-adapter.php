<?php
if (!defined('ABSPATH')) { exit; }

final class M360_MailPoet_Adapter implements M360_Newsletter_Provider_Interface
{
    private int $list_id;
    public function __construct(?int $list_id=null) { $settings=M360_Newsletter_Settings::get(); $this->list_id=$list_id?:max(1,(int)$settings['list_id']); }

    public function subscribe(string $email, string $name, array $context = [])
    {
        if (!class_exists('MailPoet\\API\\API')) { return new WP_Error('m360_mailpoet_missing', ($context['lang']??'')==='en'?'MailPoet is not available.':__('MailPoet não está ativo.', 'm360-core')); }
        try {
            $data = ['email' => $email];
            if ($name !== '') { $data['first_name'] = $name; }
            $subscriber = \MailPoet\API\API::MP('v1')->addSubscriber($data, [$this->list_id], ['send_confirmation_email' => true]);
            return ['provider_id' => (string) ($subscriber['id'] ?? ''), 'provider' => 'mailpoet'];
        } catch (\Throwable $e) { return new WP_Error('m360_mailpoet_error', $e->getMessage()); }
    }

    public function confirm(string $email)
    {
        if (!class_exists('MailPoet\\API\\API')) { return ['provider_id' => '', 'provider' => 'mailpoet_pending_setup']; }
        try {
            $subscriber = \MailPoet\API\API::MP('v1')->addSubscriber(['email' => $email], [$this->list_id], ['send_confirmation_email' => false]);
            return ['provider_id' => (string) ($subscriber['id'] ?? ''), 'provider' => 'mailpoet'];
        } catch (\Throwable $e) { return new WP_Error('m360_mailpoet_error', $e->getMessage()); }
    }

    public function unsubscribe(string $email)
    {
        if (!class_exists('MailPoet\\API\\API')) { return true; }
        try { \MailPoet\API\API::MP('v1')->unsubscribeFromLists($email, [$this->list_id]); return true; }
        catch (\Throwable $e) { return new WP_Error('m360_mailpoet_error', $e->getMessage()); }
    }
    public function status(string $email)
    {
        if (!class_exists('MailPoet\\API\\API')) { return new WP_Error('m360_mailpoet_missing', __('MailPoet não está ativo.', 'm360-core')); }
        try { return \MailPoet\API\API::MP('v1')->getSubscriber($email); }
        catch (\Throwable $e) { return new WP_Error('m360_mailpoet_error', $e->getMessage()); }
    }
    public function healthCheck(): array
    {
        $health = ['provider'=>'mailpoet', 'available'=>class_exists('MailPoet\\API\\API'), 'list_id'=>$this->list_id, 'list_available'=>false, 'list_name'=>''];
        if (!$health['available']) { return $health; }
        try {
            foreach ((array) \MailPoet\API\API::MP('v1')->getLists() as $list) {
                if ((int) ($list['id'] ?? 0) !== $this->list_id) { continue; }
                $health['list_available'] = true; $health['list_name'] = (string) ($list['name'] ?? ''); break;
            }
        } catch (\Throwable $e) { $health['error'] = $e->getMessage(); }
        return $health;
    }
    public function lists()
    {
        if (!class_exists('MailPoet\\API\\API')) { return new WP_Error('m360_mailpoet_missing','MailPoet não está ativo.'); }
        try { return (array) \MailPoet\API\API::MP('v1')->getLists(); }
        catch (\Throwable $e) { return new WP_Error('m360_mailpoet_error',$e->getMessage()); }
    }
}

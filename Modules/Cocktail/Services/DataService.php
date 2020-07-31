<?php

namespace Modules\Cocktail\Services;

//use App\Services\Service;

class DataService extends Service {
    
    /**
     * To prepare the keepContact data to be filled from request so proper data in proper manner saved
     * @param $request
     * @return array
     */
    public function prepareKeepContactSetting($request) {
        return [
            'keepContact' => [
                'page_customisation' => [
                    'keepContact_page_title'       => $request->keepContact_page_title,
                    'keepContact_page_description' => $request->keepContact_page_description,
                    'keepContact_page_logo'        => $request->keepContact_page_logo,
                    'website_page_link'            => $request->website_page_link,
                    'twitter_page_link'            => $request->twitter_page_link,
                    'linkedIn_page_link'           => $request->linkedIn_page_link,
                    'facebook_page_link'           => $request->facebook_page_link,
                    'instagram_page_link'          => $request->instagram_page_link,
                ],
                'graphics_setting'   => [
                    'main_background_color'                  => json_decode($request->main_background_color, TRUE),
                    'texts_color'                            => json_decode($request->texts_color, TRUE),
                    'keepContact_color_1'                    => json_decode($request->keepContact_color_1, TRUE),
                    'keepContact_color_2'                    => json_decode($request->keepContact_color_2, TRUE),
                    'keepContact_background_color_1'         => json_decode($request->keepContact_background_color_1, TRUE),
                    'keepContact_background_color_2'         => json_decode($request->keepContact_background_color_2, TRUE),
                    'keepContact_selected_space_color'       => json_decode($request->keepContact_selected_space_color, TRUE),
                    'keepContact_unselected_space_color'     => json_decode($request->keepContact_unselected_space_color, TRUE),
                    'keepContact_closed_space_color'         => json_decode($request->keepContact_closed_space_color, TRUE),
                    'keepContact_text_space_color'           => json_decode($request->keepContact_text_space_color, TRUE),
                    'keepContact_names_color'                => json_decode($request->keepContact_names_color, TRUE),
                    'keepContact_thumbnail_color'            => json_decode($request->keepContact_thumbnail_color, TRUE),
                    'keepContact_countdown_background_color' => json_decode($request->keepContact_countdown_background_color, TRUE),
                    'keepContact_countdown_text_color'       => json_decode($request->keepContact_countdown_text_color, TRUE),
                ],
                'section_text'       => [
                    'reply_text'                => $request->reply_text,
                    'keepContact_section_line1' => $request->keepContact_section_line1,
                    'keepContact_section_line2' => $request->keepContact_section_line2,
                ],
            ],
        ];
    }
    
    
    /**
     * To prepare the bluejeans update or create params
     * @param $request
     * @return array
     */
    public function prepareBlueJeansParam($request) {
        return [
            'event_uses_bluejeans_event' => $request->event_uses_bluejeans_event ? 1 : 0,
            'event_chat '                => $request->event_chat ? 1 : 0,
            'attendee_search '           => $request->attendee_search ? 1 : 0,
            'q_a '                       => $request->q_a ? 1 : 0,
            'allow_anonymous_questions ' => $request->allow_anonymous_questions ? 1 : 0,
            'auto_approve_questions'     => $request->auto_approve_questions ? 1 : 0,
            'auto_recording'             => $request->auto_recording ? 1 : 0,
            'phone_dial_in'              => $request->phone_dial_in ? 1 : 0,
            'raise_hand'                 => $request->raise_hand ? 1 : 0,
            'display_attendee_count'     => $request->display_attendee_count ? 1 : 0,
        ];
    }
    
    /**
     * @param $request
     * @return array
     */
    public function prepareSpaceCreateParam($request) {
        return [
            'space_name'       => $request->space_name,
            'space_short_name' => $request->space_short_name,
            'space_mood'       => $request->space_mood,
            'max_capacity'     => $request->max_capacity,
            'space_image_url'  => $request->space_image_url,
            'space_icon_url'   => $request->space_icon_url,
            'is_vip_space'     => $request->is_vip_space,
            'opening_hours'    => $request->opening_hours,
            'event_uuid'       => $request->event_uuid,
            'tags'             => $request->tags,
        ];
    }
    
    /**
     * @param $request
     * @return array
     */
    public function prepareSpaceUpdateParam($request) {
        $param = [
            'space_name'       => $request->space_name,
            'space_short_name' => $request->space_short_name,
            'space_mood'       => $request->space_mood,
            'max_capacity'     => $request->max_capacity,
            'space_image_url'  => $request->space_image_url,
            'space_icon_url'   => $request->space_icon_url,
            'is_vip_space'     => $request->is_vip_space,
            'opening_hours'    => $request->opening_hours,
            'tags'             => $request->tags,
        ];
        if($request->has('space_image_url') && $request->space_image_url) {
//            KctService::getInstance()->
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Badroom;
use App\Models\BadroomImage;
use App\Models\Homestay;
use App\Models\HomestayImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CloneController extends Controller
{
    function clone(Request $request){
        $count = 0;

        for ($i = 14188;$i > 13000 ; $i --){
            if($count >= 30){
                break;
            }
            if($this->create_data($i)) $count ++;
            echo $count;
        }

        dd('chào tuấn');
    }

    function create_data($random){
        $data = [];



        $check = true;

        try{
            $link = "https://www.luxstay.com/vi/rooms/$random";

            $html = $this->getDom1($link);

            $html = $html->load($html->__toString());

            $slide = $html->find("#single-slider",0);
            if($slide == null){
                return false;
            }
            foreach($slide->find('img') as $e)
                $data['slide'][] = $e->src;

            $name_homestay = $html->find('.single-host-avatar',0);

            $data['name_homestay'] = html_entity_decode($name_homestay->find('h1',0)->innertext);

            $data['price'] = trim($html->find('.book-it-prize',0)->find('div',0)->plaintext);

            preg_match_all('!\d+!', $data['price'], $data['price']);

            $data['caption'] = $html->find('#box_more_info',0)->find('p',1)->innertext;

            $address = $html->find('.apartment-address',0)->find('span');

            foreach ($address as $key=>$e){
                $data['address'][] = html_entity_decode($e->innertext);
            }

            $latlng = $html->find('#lst_map_detail',0);

            $data['latlng'] = [
                'data-lat' => $latlng->attr['data-lat'],
                'data-lng' => $latlng->attr['data-lng']
            ];

            $badroom = trim($html->find('.more-details',0)->find('li',2)->find('p',0)->plaintext);

            preg_match_all('!\d+!', $badroom, $badroom);

            $data['badroom_number'] = $badroom;

            $badroom_desc = $html->find('#lst_room_rates',0)->find('.lst-room-rates-item',-1)->find('.lst-content',0)->find('p',0)->innertext;

            $data['badroom_desc'] = $badroom_desc;

            $homestay = [
                'homestay_name' => $data['name_homestay'],
                'homestay_about' => $data['caption'],
                'homestay_image' => $data['slide'][0],
                'homestay_location' => implode('-',array_reverse($data['address'])),
                'homestay_latlng' => '('.implode(',',$data['latlng']).')',
                'homestay_active' => 1,
                'homestay_user_id' => 50,
                'homestay_type' => 1
            ];

            $homestay = Homestay::create($homestay);

            $badroom_data = [
                'bedroom_name' => 'phòng số 1',
                'bedroom_slot' => $data['badroom_number'][0][0],
                'bedroom_price' => implode('',$data['price'][0]),
                'bedroom_homestay_id' => $homestay->homestay_id
            ];
            $badroom = Badroom::create($badroom_data);
            $count = 0;
            foreach ($data['slide'] as $image){
                if($count < 5 || is_url_exist($image)){
                    $homestay_image[] = [
                        'homestay_image_img' => html_entity_decode($image),
                        'homestay_image_homestay_id' => $homestay->homestay_id
                    ];
                    $badroom_image[] = [
                        'bedroom_image_img' => html_entity_decode($image),
                        'bedroom_image_bedroom_id' => $badroom->bedroom_id,
                    ];

                    $count ++;
                }

            }
            if(count($badroom_image)) BadroomImage::insert($badroom_image);
            if(count($homestay_image)) HomestayImage::insert($homestay_image);

//            DB::commit();
        }catch (\Exception $exception){
//            DB::rollBack();

            $check =false;
        }

        return $check;
    }
}

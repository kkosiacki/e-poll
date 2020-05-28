<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VerifyService
{
    public function verifyFile(string $file_name) {
        if(!Storage::exists($file_name)) {
            throw ValidationException::withMessages(['file' => 'Brak pliku do walidacji']);
        } else {
            info("[VerifyService] Verify file ${file_name}");
            $client = new \GuzzleHttp\Client(['cookies' => true,'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
            ]]);
            $response = $client->post(config('e-poll.upload_url'),
                [
                    'multipart' => [

                        [
                            'name'     => 'file',
                            'contents' => Storage::get($file_name),
                            'filename' => $file_name
                        ],

                    ]
                ]
            );
            $status = json_decode($response->getBody()->getContents());
            if($status->status == 'OK') {


                info('File uploaded succesfully');
                $verify_response = $client->get(config('e-poll.verify_url'));
                if($verify_response->getStatusCode() != 200) {
                    log()->error('Wrong answer status',[$status->status]);
                } else {
                    $verify_json = $verify_response->getBody()->getContents();
                }


            } else {
                log()->error('Wrong ststaus',[$status->status]);
            }

        }
    }
}

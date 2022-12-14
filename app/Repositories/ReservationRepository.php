<?php

namespace App\Repositories;

use App\Interfaces\ReservationRepositoryInterface;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getAllReservations()
    {
        return Reservation::all();
    }

    public function getReservationById($orderId)
    {
        return Reservation::findOrFail($orderId);
    }

    public function deleteReservation($orderId)
    {
        Reservation::destroy($orderId);
    }

    public function createReservation(array $orderDetails)
    {
        return Reservation::create($orderDetails);
    }

    public function updateReservation($orderId, array $newDetails)
    {
        return Reservation::whereId($orderId)->update($newDetails);
    }

    public function getIdReservation()
    {
        return Reservation::max('id') + 1;
    }

    public function getKodeReservation()
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode =   $tanggal;
        $sub = strlen($kode) + 1;
        $index = Reservation::selectRaw('max(substring(kode,' . $sub . ')) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = Reservation::selectRaw('substring(kode,' . $sub . ') as id')
            ->get();
        $count = (int)$index->id;
        $collect_id = [];
        for ($i = 0; $i < count($collect); $i++) {
            array_push($collect_id, (int)$collect[$i]->id);
        }

        $flag = 0;
        for ($i = 0; $i < $count; $i++) {
            if ($flag == 0) {
                if (!in_array($i + 1, $collect_id)) {
                    $index = $i + 1;
                    $flag = 1;
                }
            }
        }

        if ($flag == 0) {
            $index = (int)$index->id + 1;
        }


        $index = str_pad($index, 4, '0', STR_PAD_LEFT);

        return $kode = $kode . $index;
    }

    public function getReservationWithEloquent($relation)
    {
        return Reservation::with($relation)->get();
    }
}

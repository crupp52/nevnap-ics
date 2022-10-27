<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Name;
use App\Models\NameDay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class NameDaysDataService
{
    public function getData()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML(file_get_contents('https://mek.oszk.hu/00000/00056/html/196.htm'));
        $xpath = new \DOMXPath($dom);
        $data = ((string)$xpath->query('//body/pre')->item(0)->nodeValue);
        for ($i = 0; $i < 5; $i++) {
            $data = preg_replace('/^.+\n/', '', $data);
        }
        $result = [];
        foreach (explode("\n", strip_tags($data)) as $row) {
            if (strlen($row) != 0) {
                list($name, $dates) = explode("\t", preg_replace('!\s+!', "\t", $row));
            } else {
                $dates = $row;
            }
            foreach (explode(',', $dates) as $date) {
                if (strlen($date) == 0) {
                    continue;
                }
                list($month, $day) = explode('.', $date);
                $clearDay = str_replace('*', '', $date);
                if (empty($result[$month][$day])) {
                    $result[$month][$day] = ['main' => [], 'other' => []];
                }
                $key = $clearDay == $date
                    ? 'other'
                    : 'main';
                $result[$month][$day][$key][] = $name;
            }
        }

        return $result;
    }

    public function saveToDatabase(array $dataSet)
    {
        foreach ($dataSet as $month => $days) {
            foreach ($days as $day => $data) {
                $date = Carbon::create(month: $month, day: $day);

                foreach ($data['main'] as $name) {
                    $this->createNameDay($name, $date, 1);
                }

                foreach ($data['other'] as $name) {
                    $this->createNameDay($name, $date);
                }
            }
        }
    }

    private function createNameDay(string $name, Carbon $date, int $isMain = 0): void
    {
        /** @var Name $nameObject */
        $nameObject = Name::query()->firstOrCreate([
            'name' => $name,
        ]);

        NameDay::query()->firstOrCreate([
            'name_id' => $nameObject->id,
            'is_main' => $isMain,
            'date' => $date,
        ]);
    }

    public function findMainByName(string $nameString): Collection
    {
        /** @var ?Name $name */
        $name = Name::query()->where('name', $nameString)->first();

        return $name->getMainNameDays();
    }

    public function findMainByDate(string $dateQuery): array
    {
        $dateParts = explode('-', $dateQuery);

        if (count($dateParts) !== 2) {
            dd('dasd');
        }

        $date = Carbon::create(month: $dateParts[0], day: $dateParts[1]);
        $nameDays = NameDay::query()->where('date', $date)->where('is_main', 1)->get();

        $names = [];

        foreach ($nameDays as $nameDay) {
            $names[] = $nameDay->name;
        }

        return $names;
    }

    public function autocomplete(?string $queryString): array
    {
        if (strlen($queryString) < 3) {
            return [
                'result' => [],
                'count' => 0,
            ];
        }

        $names = Name::query()
            ->where('name', 'LIKE', '%' . $queryString . '%')
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return [
            'result' => $names->toArray(),
            'count' => count($names),
        ];
    }
}

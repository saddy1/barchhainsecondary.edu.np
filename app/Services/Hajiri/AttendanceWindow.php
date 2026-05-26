<?php

namespace App\Services\Hajiri;

use App\Models\Hajiri\HajiriSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceWindow
{
    private const ARRIVAL_WINDOW_BEFORE_START_MINUTES = 240;
    private const EARLIEST_OFFICE_DAY_MINUTES = 240;
    private const DEPARTURE_WINDOW_AFTER_END_MINUTES = 360;

    public static function checkInOutRecords(Collection $attendanceLogs, HajiriSetting $setting): array
    {
        $allDayLogs = $attendanceLogs
            ->sortBy(fn ($log) => self::logDateTime($log)->timestamp)
            ->values();

        if ($allDayLogs->isEmpty()) {
            return [null, null];
        }

        return [
            $allDayLogs->first(),
            $allDayLogs->last(),
        ];
    }

    public static function summary(Collection $attendanceLogs, HajiriSetting $setting): array
    {
        [$checkInLog, $checkOutLog] = self::checkInOutRecords($attendanceLogs, $setting);

        $checkinStart = self::checkinWindowStart($setting);
        $checkinEnd = self::minutesFromTime($setting->office_start_time) + (int) $setting->late_grace_minutes;
        $checkoutStart = self::minutesFromTime($setting->office_end_time) - (int) $setting->early_grace_minutes;

        return [
            'in' => [
                'record' => $checkInLog,
                'time' => $checkInLog ? self::logTime($checkInLog) : '-',
                'valid' => $checkInLog ? self::timeIsBetween(self::logTime($checkInLog), $checkinStart, $checkinEnd) : false,
                'rule' => sprintf('Valid by %s', self::minutesToTime($checkinEnd)),
            ],
            'out' => [
                'record' => $checkOutLog,
                'time' => $checkOutLog ? self::logTime($checkOutLog) : '-',
                'valid' => $checkOutLog ? self::minutesFromTime(self::logTime($checkOutLog)) >= $checkoutStart : false,
                'rule' => sprintf('Valid from %s', self::minutesToTime($checkoutStart)),
            ],
        ];
    }

    public static function checkInOutTimes(Collection $attendanceLogs, HajiriSetting $setting): array
    {
        [$checkInLog, $checkOutLog] = self::checkInOutRecords($attendanceLogs, $setting);

        return [
            $checkInLog ? self::logTime($checkInLog) : '-',
            $checkOutLog ? self::logTime($checkOutLog) : '-',
        ];
    }

    private static function validOfficeDayLogs(Collection $attendanceLogs, HajiriSetting $setting): Collection
    {
        $officeStart = self::minutesFromTime($setting->office_start_time);
        $officeEnd = self::minutesFromTime($setting->office_end_time);

        $windowStart = self::checkinWindowStart($setting);
        $windowEnd = min(1439, $officeEnd + self::DEPARTURE_WINDOW_AFTER_END_MINUTES);

        return $attendanceLogs
            ->sortBy(fn ($log) => self::logDateTime($log)->timestamp)
            ->filter(function ($log) use ($windowStart, $windowEnd) {
                $minutes = self::minutesFromTime(self::logTime($log));

                return $minutes >= $windowStart && $minutes <= $windowEnd;
            })
            ->values();
    }

    private static function checkinWindowStart(HajiriSetting $setting): int
    {
        $officeStart = self::minutesFromTime($setting->office_start_time);

        return max(self::EARLIEST_OFFICE_DAY_MINUTES, $officeStart - self::ARRIVAL_WINDOW_BEFORE_START_MINUTES);
    }

    private static function timeIsBetween(string $time, int $start, int $end): bool
    {
        $minutes = self::minutesFromTime($time);

        return $minutes >= $start && $minutes <= $end;
    }

    private static function minutesToTime(int $minutes): string
    {
        $minutes = max(0, min(1439, $minutes));

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private static function logDateTime($log): Carbon
    {
        $value = method_exists($log, 'getRawOriginal')
            ? $log->getRawOriginal('at')
            : ($log['at'] ?? null);

        return Carbon::parse($value);
    }

    private static function logTime($log): string
    {
        return self::logDateTime($log)->format('H:i');
    }

    private static function minutesFromTime(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }
}

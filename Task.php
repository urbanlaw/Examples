<?php


abstract class Task
{
    protected const WORKING = 1;
    protected const SUCCESS = 2;
    protected const FAIL = 3;

    private int $startTime;
    private int $status;
    private array $records = [];
    private bool $ignoreSchedule = false;
    private $schedulePeriod = null;
    private $scheduleWindow = null;

    protected $data;

    protected abstract function Work();

    public static function Factory()
    {
        return new static;
    }

    public function Execute()
    {
        if($this->IsTimeToStart())
        {
            $this->startTime = time();
            $this->SetStatus(static::WORKING);
            $this->SaveReport();
            $this->LoadData();

            try
            {
                $this->Work();
                $this->SetStatus(static::SUCCESS);
            }
            catch(Throwable $e)
            {
                $this->SetStatus(static::FAIL);
                $this->AddRecord('Ошибка', strip_tags($e->getMessage()));
            }

            $this->SaveData();
            $this->SaveReport();
        }
    }

    public function IgnoreSchedule(bool $v = true)
    {
        $this->ignoreSchedule = $v;
        return $this;
    }

    protected function IsTimeToStart()
    {
        if($this->ignoreSchedule)
        {
            return true;
        }
        return $this->IsScheduleWindow($this->scheduleWindow) && $this->IsScheduleTime($this->schedulePeriod);
    }

    protected function SetStatus($v)
    {
        $this->status = $v;
        return $this;
    }

    protected function AddRecord($name, $value)
    {
        $value = substr($value, 0, 10000);
        $this->records[] = [
            'name' => $name,
            'value' => $value,
        ];
        return $this;
    }

    protected function &GetVarPointer($key)
    {
        if(!isset($this->data[$key]))
        {
            $this->data[$key] = null;
        }
        return $this->data[$key];
    }

    /*
    protected function Schedule(string $name, int $period, $window = null)
    {
        if($this->ignoreSchedule)
        {
            return true;
        }

        $lastTime = &$this->GetVarPointer('Sch_' . $name);
        $currTime = time();
        if($currTime - $lastTime >= $period)
        {
            if($this->IsScheduleWindow($window))
            {
                $lastTime = $currTime;
                $this->SaveData();
                return true;
            }
        }
        return false;
    }
    */

    protected function SetSchedule(int $period, string $window = null)
    {
        $this->schedulePeriod = $period;
        $this->scheduleWindow = $window;
    }

    protected function IsScheduleTime($period)
    {
        if($period == null)
        {
            return true;
        }

        $report = static::Report();
        if(!$report)
        {
            return true;
        }
        $lastTime = ArrLib::Get($report, 'startAt');
        $currTime = time();
        if($currTime - $lastTime >= $period)
        {
            return true;
        }
        return false;
    }

    protected function IsScheduleWindow($window)
    {
        if($window == null)
        {
            return true;
        }

        list($min, $max) = explode('-', $window);
        $min = strtotime($min);
        $max = strtotime($max);
        $current = time();
        if($min < $max)
        {
            return $min <= $current && $current < $max;
        }
        else
        {
            return $min <= $current || $current < $max;
        }
    }

    private function SaveData()
    {
        $json = JSON::Encode($this->data);
        VarsMan::Set(static::class . 'Data', $json);
    }

    private function LoadData()
    {
        $json = VarsMan::Get(static::class . 'Data');
        $this->data = $json ? JSON::Decode($json) : [];
    }

    private function SaveReport()
    {
        $data = [
            'taskName' => static::class,
            'startAt' => $this->startTime,
            'status' => $this->status,
            'workTime' => $this->status == static::WORKING ? null : time() - $this->startTime,
            'records' => $this->records,
        ];

        $json = JSON::Encode($data);
        VarsMan::Set(static::class, $json);
    }

    public static function Report()
    {
        $json = VarsMan::Get(static::class);
        if($json)
        {
            return JSON::Decode($json);
        }
        return null;
    }

    public static function StatusHtml($status)
    {
        switch($status)
        {
            case static::WORKING:
                return '<span class="ltgrey">WORKING</span>';
            case static::SUCCESS:
                return '<span class="green">SUCCESS</span>';
            case static::FAIL:
                return '<span class="red">FAIL</span>';
            default:
                return '';
        }
    }

    public static function TaskReports()
    {
        return [
            PricerTask::Report(),
            TrackerTask::Report(),
            TrackerReadyOrdersTask::Report(),
            AskReviewTask::Report(),
            NightServiceTask::Report(),
            OzonTask::Report(),
            CacheBoxberryTask::Report(),
            UpdateCityMapTask::Report(),
        ];
    }
}
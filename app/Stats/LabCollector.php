<?php

namespace App\Stats;

use App\Models\Lab;
use Prometheus\Gauge;
use Superbalist\LaravelPrometheusExporter\CollectorInterface;
use Superbalist\LaravelPrometheusExporter\PrometheusExporter;

class LabCollector implements CollectorInterface
{
    protected $machinesInUseGauge;

    protected $machinesNotInUseGauge;

    protected $machinesLockedGuage;

    public function getName()
    {
        return 'labs';
    }

    public function registerMetrics(PrometheusExporter $exporter)
    {
        $this->machinesInUseGauge = $exporter->registerGauge(
            'machines_in_use',
            'The total number of machines in use for this lab.',
            ['lab']
        );
        $this->machinesNotInUseGauge = $exporter->registerGauge(
            'machines_not_in_use',
            'The total number of machines not in use for this lab.',
            ['lab']
        );
        $this->machinesLockedGauge = $exporter->registerGauge(
            'machines_locked',
            'The total number of machines locked in this lab.',
            ['lab']
        );
    }

    /**
     * Collect metrics data, if need be, before exporting.
     *
     * As an example, this may be used to perform time consuming database queries and set the value of a counter
     * or gauge.
     */
    public function collect()
    {
        Lab::all()->each(function ($lab) {
            $this->machinesInUseGauge->set($lab->members()->online()->count(), [$lab->name]);
            $this->machinesNotInUseGauge->set($lab->members()->offline()->count(), [$lab->name]);
            $this->machinesLockedGauge->set($lab->members()->locked()->count(), [$lab->name]);
        });
    }
}

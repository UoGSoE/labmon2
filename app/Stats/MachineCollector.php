<?php

namespace App\Stats;

use App\Models\Lab;
use App\Models\Machine;
use Prometheus\Gauge;
use Superbalist\LaravelPrometheusExporter\CollectorInterface;
use Superbalist\LaravelPrometheusExporter\PrometheusExporter;

class MachineCollector implements CollectorInterface
{
    protected $machineStatusGauge;

    protected $machineTotalGuage;

    public function getName()
    {
        return 'machines';
    }

    public function registerMetrics(PrometheusExporter $exporter)
    {
        $this->machineStatusGauge = $exporter->registerGauge(
            'machines_stats',
            'The total number of machines grouped by status.',
            ['status']
        );
        $this->machineTotalGauge = $exporter->registerGauge(
            'machines_total',
            'The total number of machines.'
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
        $this->machineStatusGauge->set(Machine::online()->count(), ['in_use']);
        $this->machineStatusGauge->set(Machine::locked()->count(), ['locked']);
        $this->machineTotalGauge->set(
            Lab::with('members')->get()->sum(function ($lab) {
                return $lab->members()->count();
            })
        );
    }
}

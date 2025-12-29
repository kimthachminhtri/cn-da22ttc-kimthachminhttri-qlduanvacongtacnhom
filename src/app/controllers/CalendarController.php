<?php
/**
 * Calendar Controller
 */

namespace App\Controllers;

use App\Models\Task;
use App\Models\CalendarEvent;
use App\Middleware\AuthMiddleware;

class CalendarController extends BaseController
{
    private Task $taskModel;
    private CalendarEvent $eventModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->taskModel = new Task();
        $this->eventModel = new CalendarEvent();
    }

    public function index(): void
    {
        $month = $_GET['month'] ?? date('n');
        $year = $_GET['year'] ?? date('Y');
        
        // Get tasks with due dates for this month
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $tasks = $this->taskModel->getByDateRange($this->userId(), $startDate, $endDate);
        
        // Get calendar events for this month
        $events = $this->eventModel->getByDateRangeAll($startDate, $endDate);
        
        $this->view('calendar/index', [
            'tasks' => $tasks,
            'events' => $events,
            'currentMonth' => $month,
            'currentYear' => $year,
            'pageTitle' => 'Lịch',
        ]);
    }

    public function getEvents(): void
    {
        $start = $this->input('start');
        $end = $this->input('end');
        
        if (!$start || !$end) {
            $this->json(['error' => 'Missing date range'], 400);
            return;
        }
        
        $events = $this->eventModel->getByDateRange($this->userId(), $start, $end);
        $deadlines = $this->eventModel->getTaskDeadlines($this->userId());
        
        // Format for calendar
        $formatted = [];
        
        foreach ($events as $event) {
            $formatted[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start_time'],
                'end' => $event['end_time'],
                'color' => $event['color'] ?? '#3b82f6',
                'type' => $event['type'],
            ];
        }
        
        foreach ($deadlines as $deadline) {
            $formatted[] = [
                'id' => 'task-' . $deadline['id'],
                'title' => '📋 ' . $deadline['title'],
                'start' => $deadline['start_time'],
                'color' => '#ef4444',
                'type' => 'deadline',
            ];
        }
        
        $this->json($formatted);
    }

    public function create(): void
    {
        $errors = $this->validate([
            'title' => 'required|min:1',
            'start_time' => 'required',
        ]);

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

        $eventId = $this->eventModel->createEvent([
            'title' => $this->input('title'),
            'description' => $this->input('description', ''),
            'type' => $this->input('type', 'event'),
            'color' => $this->input('color', '#3b82f6'),
            'start_time' => $this->input('start_time'),
            'end_time' => $this->input('end_time') ?: null,
            'is_all_day' => $this->input('is_all_day') ? 1 : 0,
            'location' => $this->input('location', ''),
            'created_by' => $this->userId(),
        ]);

        if ($eventId) {
            $this->json(['success' => true, 'id' => $eventId]);
        } else {
            $this->json(['success' => false, 'error' => 'Không thể tạo sự kiện'], 500);
        }
    }

    public function update(string $id): void
    {
        $event = $this->eventModel->find($id);
        
        if (!$event || $event['created_by'] !== $this->userId()) {
            $this->json(['success' => false, 'error' => 'Event not found'], 404);
            return;
        }

        $data = [];
        if ($this->input('title')) $data['title'] = $this->input('title');
        if ($this->input('description') !== null) $data['description'] = $this->input('description');
        if ($this->input('start_time')) $data['start_time'] = $this->input('start_time');
        if ($this->input('end_time') !== null) $data['end_time'] = $this->input('end_time') ?: null;
        if ($this->input('color')) $data['color'] = $this->input('color');

        if (!empty($data)) {
            $this->eventModel->update($id, $data);
        }

        $this->json(['success' => true]);
    }

    public function delete(string $id): void
    {
        $event = $this->eventModel->find($id);
        
        if (!$event || $event['created_by'] !== $this->userId()) {
            $this->json(['success' => false, 'error' => 'Event not found'], 404);
            return;
        }

        $this->eventModel->delete($id);
        $this->json(['success' => true]);
    }
}

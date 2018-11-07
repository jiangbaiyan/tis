<?php

namespace App\Exports;

use App\Http\Model\Leave\HolidayLeave;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HolidayLeaveExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    private $holidayLeaveModelId;
    private $userId;

    public function __construct(int $holidayLeaveModelId,int $userId) {
        $this->holidayLeaveModelId = $holidayLeaveModelId;
        $this->userId = $userId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
       return HolidayLeave::join('student', 'student.id', '=', 'holiday_leave.student_id')
           ->select('student.uid', 'student.name', 'student.class', 'holiday_leave.destination', 'holiday_leave.updated_at')
           ->where('holiday_leave.holiday_leave_model_id', $this->holidayLeaveModelId)//是这个模板
           ->where('student.teacher_id', $this->userId)//自己的学生
           ->orderByDesc('holiday_leave.created_at')
           ->get();
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            '学号',
            '姓名     ',
            '班级',
            '目的地      ',
            '登记时间'
        ];
    }

}

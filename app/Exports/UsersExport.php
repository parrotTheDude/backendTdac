<?php
namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = User::query();

        // Apply filters if present
        if ($this->request->has('filter')) {
            if ($this->request->filter === 'participants') {
                $query->whereIn('user_type', ['participant', 'parent']);
            } elseif ($this->request->filter === 'external') {
                $query->where('user_type', 'external');
            } elseif ($this->request->filter === 'staff') {
                $query->whereIn('user_type', ['admin', 'superadmin']);
            }
        }

        return $query->get(['name', 'last_name', 'email', 'user_type', 'gender']);
    }
}
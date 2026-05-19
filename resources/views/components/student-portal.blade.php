<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\Complaint;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithFileUploads;

    public $step = 1;
    public $cnic = '';
    public $student_name = '';
    public $father_name = '';
    public $cnic_last_four = '';
    public $candidate_id = '';
    public $gender = '';
    public $dob = '';
    public $mobile = '';
    public $email = '';
    
    public $student = null;
    public $show_complaint_form = false;
    
    // Complaint fields
    public $complaint_category = 'student_name';
    public $complaint_details = '';
    public $attachment;
    public $complaint_submitted = false;

    public function search()
    {
        $this->student = null;
        
        // Clean the input CNIC to remove dashes for robust searching
        $cleanCnic = str_replace('-', '', $this->cnic);
        
        if ($this->step == 1) {
            $this->validate(['cnic' => 'required']);
            
            // Step 1: Only CNIC
            $this->student = Student::where(function($q) use ($cleanCnic) {
                    $q->where('cnic', $this->cnic)
                      ->orWhere('cnic', $cleanCnic)
                      ->orWhere('id_number', $this->cnic)
                      ->orWhere('id_number', $cleanCnic);
                })->first();
                
            if (!$this->student) {
                $this->step = 2;
                $this->addError('search', 'Record not found with this CNIC. Please try with Name and Last 4 digits of CNIC.');
                return;
            }
        } elseif ($this->step == 2) {
            $this->validate([
                'cnic_last_four' => 'required|digits:4',
                'student_name' => 'required',
            ]);
            
            // Step 2: Last 4 digits + Name
            $this->student = Student::where(function($q) {
                    $q->where('cnic', 'like', '%' . $this->cnic_last_four)
                      ->orWhere('id_number', 'like', '%' . $this->cnic_last_four);
                })
                ->where('student_name', 'like', '%' . $this->student_name . '%')
                ->first();
                
            if (!$this->student) {
                $this->step = 3;
                $this->addError('search', 'Still not found. Please try with Father\'s Name and Last 4 digits of CNIC.');
                return;
            }
        } elseif ($this->step == 3) {
            $this->validate([
                'cnic_last_four' => 'required|digits:4',
                'father_name' => 'required',
            ]);
            
            // Step 3: Last 4 digits + Father Name
            $this->student = Student::where(function($q) {
                    $q->where('cnic', 'like', '%' . $this->cnic_last_four)
                      ->orWhere('id_number', 'like', '%' . $this->cnic_last_four);
                })
                ->where('father_name', 'like', '%' . $this->father_name . '%')
                ->first();
                
            if (!$this->student) {
                $this->step = 4; // Inquiry Step
                return;
            }
        }
    }

    public function submitInquiry()
    {
        $this->validate([
            'cnic' => 'required',
            'student_name' => 'required',
            'father_name' => 'required',
            'candidate_id' => 'required',
            'gender' => 'required',
            'dob' => 'required|date',
            'mobile' => 'required',
            'email' => 'required|email',
        ]);

        \App\Models\NoRecordInquiry::create([
            'cnic' => $this->cnic,
            'student_name' => $this->student_name,
            'father_name' => $this->father_name,
            'candidate_id' => $this->candidate_id,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'ip_address' => request()->ip(),
        ]);

        $this->complaint_submitted = true;
        $this->step = 5; // Success Step
    }

    public function resetSearch()
    {
        $this->reset(['step', 'cnic', 'student_name', 'father_name', 'cnic_last_four', 'candidate_id', 'gender', 'dob', 'mobile', 'email', 'student', 'show_complaint_form', 'complaint_submitted']);
    }

    public function submitComplaint()
    {
        $rules = [
            'complaint_category' => 'required',
            'complaint_details' => 'required|min:10',
        ];

        // Only require attachment if the category is picture
        if ($this->complaint_category === 'picture') {
            $rules['attachment'] = 'required|image|max:2048'; // 2MB Max
        }

        $this->validate($rules);

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('complaints_attachments', 'public');
        }

        Complaint::create([
            'student_id' => $this->student->id,
            'category' => $this->complaint_category,
            'problem_details' => $this->complaint_details,
            'status' => 'pending',
            'attachment_path' => $path,
        ]);

        $this->complaint_submitted = true;
        $this->show_complaint_form = false;
        $this->complaint_details = '';
        $this->attachment = null;
    }
};
?>

<div class="max-w-4xl mx-auto px-4">
    @if($step == 5)
        <div class="glass rounded-3xl p-12 shadow-xl text-center">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Inquiry Submitted</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">
                We couldn't find your record automatically. Your details (CNIC: {{ $cnic }}) have been sent to our administration for manual verification. Please check back later or contact support if you have any questions.
            </p>
            <button wire:click="resetSearch" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-all">
                Search Again
            </button>
        </div>
    @elseif(!$student)
        <div class="glass rounded-3xl p-8 shadow-xl">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                    @if($step == 4) Report Missing Record @else Find Your Admit Card @endif
                </h2>
                <p class="text-gray-500">
                    @if($step == 4) 
                        All search methods failed. Please provide your full details so we can manually verify your record.
                    @else
                        Enter your details to download your examination admittance card.
                    @endif
                </p>
            </div>

            @if ($errors->has('search'))
                <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 text-amber-700 rounded-r-lg flex items-start space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $errors->first('search') }}</span>
                </div>
            @endif

            @if($step == 4)
                <form wire:submit.prevent="submitInquiry" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Candidate ID</label>
                            <input type="text" wire:model="candidate_id" placeholder="Enter your ID" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('candidate_id') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Student Name</label>
                            <input type="text" wire:model="student_name" placeholder="Full name as per application" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('student_name') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Father's Name</label>
                            <input type="text" wire:model="father_name" placeholder="Full name as per application" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('father_name') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">CNIC Number</label>
                            <input type="text" wire:model="cnic" placeholder="e.g. 3520212345671" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('cnic') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Gender</label>
                            <select wire:model="gender" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Date of Birth</label>
                            <input type="date" wire:model="dob" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('dob') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Mobile Number</label>
                            <input type="text" wire:model="mobile" placeholder="e.g. 03001234567" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('mobile') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Email Address</label>
                            <input type="email" wire:model="email" placeholder="e.g. name@example.com" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('email') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-center pt-4">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-12 rounded-2xl shadow-lg transition-all transform hover:scale-105">
                            Submit Inquiry
                        </button>
                    </div>
                    <div class="text-center">
                        <button type="button" wire:click="resetSearch" class="text-sm text-gray-400 hover:text-indigo-600 underline">Start over</button>
                    </div>
                </form>
            @else
                <form wire:submit.prevent="search" class="space-y-6">
                    @if($step == 1)
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">CNIC Number</label>
                            <input type="text" wire:model="cnic" placeholder="e.g. 3520212345671" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            @error('cnic') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                        </div>
                    @elseif($step == 2)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Last 4 Digits of CNIC</label>
                                <input type="text" wire:model="cnic_last_four" maxlength="4" placeholder="e.g. 4567" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                                @error('cnic_last_four') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Student Name</label>
                                <input type="text" wire:model="student_name" placeholder="As per application" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                                @error('student_name') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @elseif($step == 3)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Last 4 Digits of CNIC</label>
                                <input type="text" wire:model="cnic_last_four" maxlength="4" placeholder="e.g. 4567" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                                @error('cnic_last_four') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Father's Name</label>
                                <input type="text" wire:model="father_name" placeholder="As per application" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                                @error('father_name') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-center pt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-12 rounded-2xl shadow-lg transition-all transform hover:scale-105">
                            Search Record
                        </button>
                    </div>
                    
                    @if($step > 1)
                        <div class="text-center">
                            <button type="button" wire:click="resetSearch" class="text-sm text-gray-400 hover:text-indigo-600 underline">Start over</button>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    @else
        <div class="space-y-8">
            <div class="glass rounded-3xl p-8 shadow-xl overflow-hidden relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600 opacity-5 rounded-bl-full"></div>
                
                <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                    <div class="relative">
                        <div class="w-40 h-48 rounded-2xl overflow-hidden border-4 border-white shadow-lg bg-gray-100">
                            @php
                                $pictureUrl = $student->picture_path;
                                // Build a cache buster from the file's last modified time
                                $parsedPath = parse_url($pictureUrl, PHP_URL_PATH);
                                $cleanPath = ltrim($parsedPath ?: $pictureUrl, '/');
                                if (str_starts_with($cleanPath, 'storage/')) {
                                    $cleanPath = substr($cleanPath, 8);
                                }
                                $localFile = storage_path('app/public/' . $cleanPath);
                                $cacheBuster = file_exists($localFile) ? filemtime($localFile) : time();
                                $pictureUrl = $pictureUrl . '?v=' . $cacheBuster;
                            @endphp
                            <img src="{{ $pictureUrl }}" alt="{{ $student->student_name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-3 -right-3 w-12 h-12 bg-green-500 rounded-full border-4 border-white flex items-center justify-center text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900">{{ $student->student_name }}</h2>
                        <p class="text-xl text-indigo-600 font-medium mb-4">{{ $student->student_id }}</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white/50 p-3 rounded-xl">
                                <span class="block text-gray-400 uppercase text-xs font-bold mb-1">Father's Name</span>
                                <span class="font-semibold text-gray-800">{{ $student->father_name }}</span>
                            </div>
                            <div class="bg-white/50 p-3 rounded-xl">
                                <span class="block text-gray-400 uppercase text-xs font-bold mb-1">CNIC</span>
                                <span class="font-semibold text-gray-800">{{ $student->id_number }}</span>
                            </div>
                            <div class="bg-white/50 p-3 rounded-xl">
                                <span class="block text-gray-400 uppercase text-xs font-bold mb-1">Centre Name</span>
                                <span class="font-semibold text-gray-800">{{ $student->centre }}</span>
                            </div>
                            <div class="bg-white/50 p-3 rounded-xl">
                                <span class="block text-gray-400 uppercase text-xs font-bold mb-1">Roll Number</span>
                                <span class="font-semibold text-gray-800">{{ $student->sorted_roll_number_uhs ?? 'Pending' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('admit-card.download', $student->student_id) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-indigo-100 flex items-center justify-center space-x-3 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download Admittance Card (PDF)</span>
                    </a>
                    
                    <!-- <button wire:click="$set('show_complaint_form', true)" class="bg-white border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-4 px-8 rounded-2xl transition-all">
                        Request Correction
                    </button> -->
                    
                    <button wire:click="resetSearch" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-4 px-6 rounded-2xl transition-all">
                        Back
                    </button>
                </div>
            </div>

            @if($show_complaint_form)
                <div class="glass rounded-3xl p-8 shadow-xl border-t-4 border-indigo-600 animate-fade-in">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Submit Correction Request</h3>
                    
                    <form wire:submit.prevent="submitComplaint" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Category for Correction</label>
                                <select wire:model.live="complaint_category" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                                    <option value="student_name">Student Name</option>
                                    <option value="father_name">Father Name</option>
                                    <option value="date_of_birth">Date of Birth</option>
                                    <option value="cnic">CNIC</option>
                                    <option value="picture">Profile Picture / Image Correction</option>
                                    <option value="exam_center">Exam Center</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        @if($complaint_category === 'picture')
                        <div class="space-y-2 animate-fade-in">
                            <label class="text-sm font-semibold text-gray-700">Upload Correct Profile Picture</label>
                            <input type="file" wire:model="attachment" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('attachment') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            
                            @if ($attachment)
                                <div class="mt-2">
                                    <img src="{{ $attachment->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-xl shadow-sm border border-gray-200">
                                </div>
                            @endif
                        </div>
                        @endif
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Detailed Description of Problem</label>
                            <textarea wire:model="complaint_details" rows="4" placeholder="Explain what needs to be corrected and provide the correct information..." class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                            @error('complaint_details') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-all">
                                Submit Request
                            </button>
                            <button type="button" wire:click="$set('show_complaint_form', false)" class="text-gray-500 font-semibold">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($complaint_submitted)
                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-3xl animate-bounce-short">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-500 rounded-full p-2 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-green-800">Request Submitted Successfully</h4>
                            <p class="text-green-700">Your complaint has been logged. Admin will review and update your record soon.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
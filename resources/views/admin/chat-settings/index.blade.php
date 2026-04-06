@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            {{ __('Chat Settings') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-xl">
                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline><line x1="12" y1="12" x2="20" y2="7.5"></line><line x1="12" y1="12" x2="12" y2="21"></line><line x1="12" y1="12" x2="4" y2="7.5"></line></svg>
                            </div>
                            <div class="ms-3">
                                <h4 class="alert-title">Success</h4>
                                <div class="text-secondary">{{ session('success') }}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            </div>
                            <div class="ms-3">
                                <h4 class="alert-title">Error</h4>
                                <div class="text-secondary">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Settings Cards --}}
                @foreach ($settings as $section => $sectionSettings)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ ucwords(str_replace('_', ' ', $section)) }}
                            </h3>
                            <div class="card-options">
                                @if ($section === 'prompt')
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewPrompt()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4 -8 11 -8s11 8 11 8s-4 8 -11 8s-11 -8 -11 -8"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        {{ __('Preview') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($sectionSettings as $setting)
                                <div class="mb-3">
                            <form method="POST" action="{{ route('admin.chat-settings.update', $setting) }}">
                                @csrf
                                @method('PUT')
                                        <label class="form-label">
                                            {{ str_replace('_', ' ', ucfirst($setting->key)) }}
                                        </label>

                                        @if ($setting->type === 'textarea')
                                            <textarea 
                                                name="value" 
                                                rows="3" 
                                                class="form-control"
                                                placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}"
                                            >{{ $setting->value }}</textarea>
                                        @elseif ($setting->type === 'email')
                                            <input 
                                                type="email" 
                                                name="value" 
                                                value="{{ $setting->value }}" 
                                                class="form-control"
                                                placeholder="Enter email address"
                                            />
                                        @elseif ($setting->type === 'phone')
                                            <input 
                                                type="tel" 
                                                name="value" 
                                                value="{{ $setting->value }}" 
                                                class="form-control"
                                                placeholder="Enter phone number"
                                            />
                                        @elseif ($setting->type === 'number')
                                            <input 
                                                type="number" 
                                                name="value" 
                                                value="{{ $setting->value }}" 
                                                class="form-control"
                                                placeholder="Enter number"
                                            />
                                        @else
                                            <input 
                                                type="text" 
                                                name="value" 
                                                value="{{ $setting->value }}" 
                                                class="form-control"
                                                placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}"
                                            />
                                        @endif

                                        @if ($setting->description)
                                            <small class="form-hint">{{ $setting->description }}</small>
                                        @endif

                                        <button type="submit" class="btn btn-primary mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21h-14a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h10l5 5v11a2 2 0 0 1 -2 2z"></path><polyline points="17 3 17 8 22 8"></polyline><line x1="12" y1="11" x2="12" y2="17"></line><polyline points="9 14 12 11 15 14"></polyline></svg>
                                            {{ __('Save') }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Suggested Questions Section --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Suggested Questions</h3>
                        <span class="badge badge-success">Manage</span>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary">Add questions that appear in the chat widget when there's no message history</p>
                        
                        <form method="POST" action="{{ route('admin.chat-settings.update-questions') }}" id="suggestedQuestionsForm">
                            @csrf
                            <div id="questions-container">
                                @php
                                    $questionsJson = \App\Models\ChatSetting::get('suggested_questions', '[]');
                                    $questions = is_string($questionsJson) ? json_decode($questionsJson, true) : $questionsJson;
                                    $questions = is_array($questions) ? $questions : [];
                                @endphp
                                
                                @forelse ($questions as $index => $question)
                                    <div class="input-group mb-2" data-question-group>
                                        <input type="text" name="questions[]" value="{{ $question }}" class="form-control" placeholder="Enter suggested question" />
                                        <button class="btn btn-outline-danger" type="button" onclick="removeQuestion(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14"></path><path d="M8 6v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </div>
                                @empty
                                    <div class="input-group mb-2" data-question-group>
                                        <input type="text" name="questions[]" value="" class="form-control" placeholder="Enter suggested question" />
                                        <button class="btn btn-outline-danger" type="button" onclick="removeQuestion(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14"></path><path d="M8 6v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </div>
                                @endforelse
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="addQuestion()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Add Question
                            </button>
                            
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21h-14a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h10l5 5v11a2 2 0 0 1 -2 2z"></path><polyline points="17 3 17 8 22 8"></polyline><line x1="12" y1="11" x2="12" y2="17"></line><polyline points="9 14 12 11 15 14"></polyline></svg>
                                Save Questions
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div class="modal modal-blur fade" id="promptModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('AI System Prompt Preview') }}</h5>
                </div>
                <div class="modal-body">
                    <div id="prompt-preview" class="bg-light p-3 rounded text-dark" style="max-height: 500px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; font-size: 0.85rem;">
                        <p class="text-dark">Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link" data-bs-dismiss="modal">{{ __('Close') }}</a>
                    <button type="button" class="btn btn-primary" onclick="copyPrompt()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                        {{ __('Copy to Clipboard') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPrompt() {
            const url = "{{ route('admin.chat-settings.preview-prompt') }}";
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('prompt-preview').textContent = data.prompt;
                    const modal = new bootstrap.Modal(document.getElementById('promptModal'));
                    modal.show();
                })
                .catch(error => {
                    alert('Error loading prompt: ' + error);
                });
        }

        function copyPrompt() {
            const promptText = document.getElementById('prompt-preview').innerText;
            navigator.clipboard.writeText(promptText).then(() => {
                alert('{{ __("Prompt copied to clipboard!") }}');
            }).catch(err => {
                alert('{{ __("Failed to copy") }}: ' + err);
            });
        }

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const newGroup = document.createElement('div');
            newGroup.className = 'input-group mb-2';
            newGroup.setAttribute('data-question-group', '');
            newGroup.innerHTML = `
                <input type="text" name="questions[]" value="" class="form-control" placeholder="Enter suggested question" />
                <button class="btn btn-outline-danger" type="button" onclick="removeQuestion(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14"></path><path d="M8 6v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                </button>
            `;
            container.appendChild(newGroup);
        }

        function removeQuestion(button) {
            const group = button.closest('[data-question-group]');
            if (group) {
                group.remove();
            }
        }

        // Handle form submission
        document.getElementById('suggestedQuestionsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const questions = [];
            const inputs = document.querySelectorAll('input[name="questions[]"]');
            inputs.forEach(input => {
                const value = input.value.trim();
                if (value) {
                    questions.push(value);
                }
            });

            if (questions.length === 0) {
                alert('{{ __("Please add at least one question") }}');
                return;
            }

            // Submit via AJAX
            fetch('{{ route("admin.chat-settings.update-questions") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ questions: questions })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.style.margin = '20px';
                    alert.innerHTML = `
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline><line x1="12" y1="12" x2="20" y2="7.5"></line><line x1="12" y1="12" x2="12" y2="21"></line><line x1="12" y1="12" x2="4" y2="7.5"></line></svg>
                            </div>
                            <div class="ms-3">
                                <h4 class="alert-title">Success</h4>
                                <div class="text-secondary">${data.message || '{{ __("Questions saved successfully!") }}'}</div>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    `;
                    
                    // Insert at top of page body
                    const firstElement = document.body.firstChild;
                    if (firstElement) {
                        document.body.insertBefore(alert, firstElement);
                    } else {
                        document.body.appendChild(alert);
                    }
                    
                    // Auto-dismiss after 3 seconds
                    setTimeout(() => alert.remove(), 3000);
                } else {
                    alert('{{ __("Error saving questions: ") }}' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('{{ __("Error: ") }}' + error);
            });
        });
    </script>
@endsection

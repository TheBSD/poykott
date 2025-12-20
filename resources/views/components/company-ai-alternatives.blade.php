@props([
    'company',
    'aiAlternative' => null,
])

<div
    class="rounded-xl border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-blue-50 p-6 shadow-lg dark:border-purple-700 dark:from-purple-900/20 dark:to-blue-900/20"
    x-data="{
        isGenerating: false,
        contentHtml: @js($aiAlternative ? \Illuminate\Support\Str::markdown($aiAlternative->content, ['html_input' => 'strip', 'allow_unsafe_links' => false]) : ''),
        hasExisting: @js($aiAlternative !== null),

        init() {
            if (! this.hasExisting) {
                this.generate()
            }
        },

        async generate() {
            this.isGenerating = true

            try {
                const response = await fetch(
                    '{{ route('companies.ai-alternatives.store', $company) }}',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    },
                )

                const data = await response.json()

                if (data.success) {
                    this.contentHtml = data.html
                    this.hasExisting = true
                    this.$nextTick(() => {
                        this.$el.querySelectorAll('a').forEach((link) => {
                            link.target = '_blank'
                            link.rel = 'noopener noreferrer'
                            link.classList.add(
                                'inline-block',
                                'truncate',
                                'text-blue-600',
                                'hover:text-blue-800',
                                'hover:underline',
                            )
                        })
                    })
                } else {
                    this.contentHtml =
                        '<p class=&quot;text-red-600&quot;>Failed to generate alternatives: ' +
                        (data.message || 'Unknown error') +
                        '</p>'
                }
            } catch (error) {
                this.contentHtml =
                    '<p class=&quot;text-red-600&quot;>Failed to generate alternatives: ' +
                    error.message +
                    '</p>'
            } finally {
                this.isGenerating = false
            }
        },
    }"
>
    <div class="mb-4">
        <div class="flex items-center gap-2 flex-col md:flex-row">
            <svg
                class="h-6 w-6 text-purple-600 dark:text-purple-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">AI-Generated Alternatives</h3>
            <span
                class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-800 dark:text-purple-100"
            >
                Powered by AI
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500"><em>May contain mistakes</em></span>
        </div>
    </div>

    <div x-show="isGenerating" class="flex items-center gap-3 py-4">
        <svg class="h-5 w-5 animate-spin text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>
        <span class="text-sm text-gray-600 dark:text-gray-300">AI is thinking and generating alternatives...</span>
    </div>

    <div
        x-show="contentHtml"
        x-html="contentHtml"
        x-init="
            $nextTick(() => {
                $el.querySelectorAll('a').forEach((link) => {
                    link.target = '_blank'
                    link.rel = 'noopener noreferrer'
                })
            })
        "
        @content-updated.window="$nextTick(() => {
            $el.querySelectorAll('a').forEach(link => {
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
            });
        })"
        class="ai-alternatives-content prose prose-sm dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-gray-100 prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-strong:text-gray-900 dark:prose-strong:text-gray-100 max-w-none [&>h2]:mb-4 [&>h2]:mt-8 [&>h3]:mb-3 [&>h3]:mt-6 [&>p]:mb-4 [&>ul>li]:my-2 [&>ul]:space-y-3"
    ></div>
</div>

<style>
    .ai-alternatives-content a {
        display: inline-block;
        color: #2563eb;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 100%;
    }
    .ai-alternatives-content a:hover {
        color: #1e40af;
        text-decoration: underline;
    }
    .dark .ai-alternatives-content a {
        color: #60a5fa;
    }
    .dark .ai-alternatives-content a:hover {
        color: #93c5fd;
    }
</style>

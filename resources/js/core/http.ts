// resources/js/core/http.ts

export async function api<T>(url: string, options?: RequestInit): Promise<T> {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const defaultOptions: RequestInit = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            ...options?.headers,
        },
        credentials: 'same-origin',
    }

    const response = await fetch(url, { ...defaultOptions, ...options })

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
    }

    return response.json()
}

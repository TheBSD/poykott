<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">About Us</h1>
    
    <div class="prose max-w-none mb-12">
        <p>Boycott Israeli Tech is a project that aims to raise awareness 
            about the Israeli tech industry and its connections to the Israeli 
            government and military. We believe that by boycotting Israeli tech, 
            we can help bring about change and hold those responsible for human rights 
            abuses accountable.</p>
    </div>

    <div class="bg-gray-100 p-6 rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
        
        <form action="{{ route('contact') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" class="w-full md:w-1/2 p-2 border rounded-md" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input type="email" name="email" class="w-full md:w-1/2 p-2 border rounded-md" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Message</label>
                <textarea name="message" rows="4" class="w-full md:w-1/2 p-2 border rounded-md" required></textarea>
            </div>
            
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Send Message
            </button>
        </form>
    </div>
</div>

</x-app-layout>
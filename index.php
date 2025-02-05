<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative flex flex-col items-center justify-center min-h-screen bg-gray-100">

   <!-- Background Image with Overlay -->
   <div class="absolute inset-0 bg-cover bg-center z-[-2]" style="background-image: url('backone.jpg');"></div>
   <div class="absolute inset-0 bg-black/60 z-[-1]"></div> <!-- Overlay added here -->

   <!-- Centered Login Form with Highlight Effect -->
   <div class="form w-full max-w-sm rounded-md shadow-2xl overflow-hidden z-[100] relative cursor-pointer snap-start shrink-0 py-6 px-8 bg-[#DFA16A] flex flex-col items-center justify-center gap-5 transition-all duration-300">
      <p class="text-[#A15A3E] text-xl font-semibold flex items-center justify-center gap-2">
        <i class="fa-solid fa-paw"></i> Pet Sign In Account
      </p>

      <form action="" class="flex flex-col gap-4 w-full">
        <div class="flex flex-col">
          <label for="email" class="text-sm text-[#7F3D27] font-semibold">Email</label>
          <input
            type="email"
            placeholder="Enter Your Email"
            class="w-full py-2 px-3 bg-transparent outline-none border-b-2 border-[#7F3D27] placeholder:text-[#A15A3E] text-[#7F3D27]"
          />
        </div>

        <div class="flex flex-col">
          <label for="password" class="text-sm text-[#7F3D27] font-semibold">Password</label>
          <input
            type="password"
            placeholder="Enter Your Password"
            class="w-full py-2 px-3 bg-transparent outline-none border-b-2 border-[#7F3D27] placeholder:text-[#A15A3E] text-[#7F3D27]"
          />
        </div>

        <div class="flex items-center gap-2 text-[#A15A3E]">
          <input type="checkbox" class="w-4 h-4 accent-[#A15A3E]" checked />
          <p class="text-xs">By signing in, you agree to the <span class="font-semibold">Terms & Policy</span></p>
        </div>

        <button class="w-full px-6 font-semibold text-sm py-3 rounded-md hover:scale-105 transition-all text-[#7F3D27] bg-[#D9D9D9] shadow-lg">
          Sign In
        </button>
      </form>
   </div>

   <!-- Copyright Section -->
   <footer class="absolute bottom-4 text-center text-sm text-gray-200 z-[100]">
     &copy; 2025 Pet Login System. All Rights Reserved.
   </footer>

</body>
</html>

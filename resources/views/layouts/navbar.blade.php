<nav class="flex items-center justify-between flex-wrap bg-blue-500 p-6 mb-4 shadow">
  <div class="flex items-center flex-shrink-0 text-white mr-6">
    <a href="/" class="font-semibold text-xl tracking-tight">
      Labmon
    </a>
  </div>
  <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
    <div class="text-sm lg:flex-grow">
      <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-blue-200 hover:text-white mr-4">
        Labs
      </a>
      <a href="{{ route('machine.index') }}" class="block mt-4 lg:inline-block lg:mt-0 text-blue-200 hover:text-white mr-4">
        Machines
      </a>
      <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-blue-200 hover:text-white mr-4">
        Stats
      </a>
      <a href="{{ route('options.edit') }}" class="block mt-4 lg:inline-block lg:mt-0 text-blue-200 hover:text-white">
        Options
      </a>
    </div>
    <div>
      <form action="{{ route('logout') }}" method="post">
        @csrf
        <button class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-blue-500 hover:bg-white mt-4 lg:mt-0">Logout</button>
      </form>
    </div>
  </div>
</nav>
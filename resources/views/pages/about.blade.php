<x-layouts.public title="About Us">

    <div class="bg-base-200 border-b border-base-300 py-10 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">About Livingston Autos</h1>
            <p class="text-[14px] text-base-content/50 mt-1">Quality Japanese & Korean imports, delivered to your door</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 lg:px-8 py-14 space-y-8">
        <div>
            <h2 class="text-[18px] font-semibold text-base-content mb-3">Our Story</h2>
            <p class="text-[14px] text-base-content/60 leading-relaxed">
                Livingston Autos was founded to make buying a quality imported car in Ghana simple and
                trustworthy. We source directly from auction houses and dealers in Japan and Korea, so every
                vehicle that reaches our customers has been inspected and verified before it ever leaves the
                port of origin.
            </p>
        </div>

        <div>
            <h2 class="text-[18px] font-semibold text-base-content mb-3">Where We Source From</h2>
            <p class="text-[14px] text-base-content/60 leading-relaxed">
                Japan and Korea are home to some of the world's most reliable, well-maintained used vehicles —
                strict inspection standards and a culture of meticulous car care mean our imports consistently
                outperform vehicles sourced elsewhere. We work with trusted partners in both countries to bring
                that quality straight to Ghana.
            </p>
        </div>

        <div>
            <h2 class="text-[18px] font-semibold text-base-content mb-3">Why Customers Trust Us</h2>
            <p class="text-[14px] text-base-content/60 leading-relaxed">
                From the moment you place an order to the day your car clears customs, we keep you updated at
                every stage — payment confirmation, shipping, arrival, and clearing. No surprises, no hidden
                fees, just a clear path from browsing to driving.
            </p>
        </div>

        <div class="flex flex-wrap gap-4 pt-4">
            <a href="{{ route('cars.index') }}" class="inline-flex items-center justify-center bg-primary text-white text-[14px] font-bold py-3 px-6 rounded-full hover:bg-primary/90 transition-colors">
                Browse Our Cars
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center bg-base-200 text-base-content text-[14px] font-bold py-3 px-6 rounded-full hover:bg-base-300 transition-colors">
                Get in Touch
            </a>
        </div>
    </div>

</x-layouts.public>

<x-layouts.public :title="$car->year . ' ' . $car->make->name . ' ' . $car->carModel->name">

    <livewire:cars.car-detail :car="$car" />

</x-layouts.public>

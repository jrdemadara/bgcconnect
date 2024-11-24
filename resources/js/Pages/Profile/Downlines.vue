<script setup>
import { ref, onMounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { GitFork } from "lucide-vue-next";
import * as d3 from "d3";

// Define the prop to accept data
const props = usePage().props;
const data = JSON.parse(props.data); // Parse the JSON string into an object
console.log(data);

// Create a reference for the chart container
const chartRef = ref(null);

const createChart = (data) => {
    // Specify the chart’s dimensions
    const width = 1800; // Increased width for zoomability
    const height = 1800; // Increased height for zoomability
    const cx = width * 0.5; // center x
    const cy = height * 0.5; // center y
    const radius = Math.min(width, height) / 1.2; // radius for the radial tree layout

    // Create a radial tree layout
    const tree = d3
        .tree()
        .size([2 * Math.PI, radius])
        .separation((a, b) => {
            // Adjust the separation distance here
            const separationFactor = 1; // You can change this value to increase/decrease spacing
            return (a.parent === b.parent ? separationFactor : 2) / a.depth;
        });

    // Create the root node from the data
    const root = d3
        .hierarchy(data)
        .sort((a, b) => d3.ascending(a.data.name, b.data.name));

    // Apply the tree layout
    tree(root);

    // Clear previous chart
    d3.select(chartRef.value).selectAll("*").remove();

    // Create the SVG container
    const svg = d3
        .create("svg")
        .attr("width", width)
        .attr("height", height)
        .attr("viewBox", [-cx, -cy, width, height])
        .attr("style", "width: 100%; height: auto; font: 10px sans-serif;");

    // Append links
    svg.append("g")
        .attr("fill", "none")
        .attr("stroke", "#555")
        .attr("stroke-opacity", 0.4)
        .attr("stroke-width", 1.5)
        .selectAll("path")
        .data(root.links())
        .join("path")
        .attr(
            "d",
            d3
                .linkRadial()
                .angle((d) => d.x)
                .radius((d) => d.y)
        );

    // Append nodes
    svg.append("g")
        .selectAll("circle")
        .data(root.descendants())
        .join("circle")
        .attr(
            "transform",
            (d) => `rotate(${(d.x * 180) / Math.PI - 90}) translate(${d.y},0)`
        )
        .attr("fill", (d) => (d.children ? "#555" : "#999"))
        .attr("r", 2.5);

    // Append labels
    svg.append("g")
        .attr("stroke-linejoin", "round")
        .attr("stroke-width", 3)
        .selectAll("text")
        .data(root.descendants())
        .join("text")
        .attr(
            "transform",
            (d) =>
                `rotate(${(d.x * 180) / Math.PI - 90}) translate(${
                    d.y
                },0) rotate(${d.x >= Math.PI ? 180 : 0})`
        )
        .attr("dy", "0.31em")
        .attr("x", (d) => (d.x < Math.PI === !d.children ? 6 : -6))
        .attr("text-anchor", (d) =>
            d.x < Math.PI === !d.children ? "start" : "end"
        )
        .attr("paint-order", "stroke")
        .attr("stroke", "white")
        .attr("fill", "currentColor")
        .text((d) => d.data.name);

    // Append the SVG to the chart container
    d3.select(chartRef.value).append(() => svg.node());
};

// Lifecycle hook to create the chart when the component is mounted
onMounted(() => {
    createChart(data); // Call createChart with initial data
});

// Watch for changes in the data prop
watch(
    () => props.data,
    (newData) => {
        createChart(newData); // Recreate the chart when data changes
    }
);
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex flex-col items-center py-6 h-screen bg-white">
            <div class="rounded-lg p-2 ring-1 ring-slate-200">
                <GitFork class="text-black mr-0.5" :size="26" />
            </div>
            <h2 class="font-bold text-xl mt-4">Downlines</h2>
            <p class="font-light text-xs mt-1 text-slate-700">
                Your downline network visualization.
            </p>
            <div class="w-full scroll-smooth max-w-1200px max-h-800px">
                <div ref="chartRef" class="w-full h-full"></div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

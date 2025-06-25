<script setup lang="ts">
import { useRoute } from 'vue-router';
import FeaturedReview from '@/components/FeaturedReview.vue';
import KeyDetails from '@/components/KeyDetails.vue';
import Rating from '@/components/Rating.vue';

const route = useRoute();

const listingId = route.params.id;

</script>

<template>
  <div class="container mx-auto p-4">
    <UButton icon="i-heroicons-arrow-left" variant="link" class="mb-4">Go Back</UButton>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center">
          <UAvatar src="https://i.pravatar.cc/150?u=a042581f4e29026704d" size="2xl" />
          <div class="ml-4">
            <h1 class="text-3xl font-bold">Eric</h1>
            <p class="text-gray-500">ELECTRIC WITH ERIC</p>
            <Rating :rating="5" :reviews="1" />
          </div>
          <UButton class="ml-auto">Contact</UButton>
        </div>

        <div class="mt-8">
          <h2 class="text-2xl font-bold mb-4">Featured Review</h2>
          <FeaturedReview />
        </div>

        <div class="mt-8">
          <h2 class="text-2xl font-bold mb-4">What I'm Sharing</h2>
          <div class="flex space-x-2">
            <UBadge>guitar</UBadge>
            <UBadge>acoustic</UBadge>
            <UBadge>electric</UBadge>
          </div>
        </div>

        <div class="mt-8">
          <h2 class="text-2xl font-bold mb-4">Who am I?</h2>
          <p>
            My speciality is in beginners and intermediate players on both acoustic and electric guitar. Genres of speciality include blues and rock from the likes of Hendrix, Led Zeppelin, Pink Floyd, AC/DC to the funk of James Brown, Nile Rogers, all the way to current Top 40 Pop. My teaching style isn't just about a student learning to play a song. There's tons of videos on YouTube that achieve that goal. What differs in this case, is a focus on creating a fun environment where students get a solid musical foundation, learn harmony and theory and are encouraged to express themselves creatively.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
          <div>
            <h2 class="text-2xl font-bold mb-4">Qualifications</h2>
            <ul class="list-disc list-inside">
              <li>Google Tech - Masters</li>
            </ul>
          </div>
          <div>
            <h2 class="text-2xl font-bold mb-4">Trainerfly Spotlight</h2>
            <div class="aspect-w-16 aspect-h-9">
              <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
          </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Key Details</h2>
            <KeyDetails />
        </div>


        <div class="mt-8">
          <h2 class="text-2xl font-bold mb-4">Other Skills</h2>
          <div class="flex space-x-2">
            <UBadge>gardening</UBadge>
            <UBadge>Call of Duty</UBadge>
            <UBadge>skydining</UBadge>
          </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Reviews</h2>
            <!-- Dummy reviews -->
            <div>
                <h3 class="font-bold">Amazing Teacher!</h3>
                <p>"He has a unique ability to relay concepts in a way that connects. He give you the whole picture and then break down the process into simple pieces for you to learn, all so that you can put it all back together again! Very unique! - <span class="font-bold">Craig S</span></p>
            </div>
            <div class="mt-4">
                <h3 class="font-bold">Knowledgeable and great instructor</h3>
                <p>"Eric showed and teach me, Guitar exercises to improve the flow of my fingers. Eric is very patient person I can tell since I am a beginner. Looking forward to continue my guitar learning process with Eric. - <span class="font-bold">David C</span></p>
            </div>
            <div class="mt-4">
                <h3 class="font-bold">great teacher, professional and talented</h3>
                <p>"Our son Liam Fares just finished his first class, he was very motivated to practice immediately after his class. Thank you for your hard work and support. - <span class="font-bold">David F</span></p>
            </div>
        </div>

      </div>

      <div class="md:col-span-1">
        <h2 class="text-2xl font-bold mb-4">Scheduling</h2>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <h3 class="font-bold">DEC Availability</h3>
                <div class="text-sm text-gray-500">
                    <p>SUN: NONE - NONE</p>
                    <p>MON: NONE - NONE</p>
                    <p>TUE: NONE - NONE</p>
                    <p>WED: NONE - NONE</p>
                    <p>THU: 2:00PM - 8:00PM</p>
                    <p>FRI: 2:00PM - 8:00PM</p>
                    <p>SAT: 2:00PM - 8:00PM</p>
                </div>
            </div>
            <UCalendar />
            <UButton block>Make an appointment</UButton>
        </div>
      </div>
    </div>
     <div class="text-center mt-8">
        <UButton>Contact</UButton>
    </div>
  </div>
</template>

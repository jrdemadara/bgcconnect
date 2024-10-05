<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import cameraSound from "../../../../sound/camera.mp3";
import { toast } from "vue3-toastify";

const video = ref<HTMLVideoElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
const photo = ref<string | null>(null);
const profilePhoto = ref<File | null>(null);
const captureSoundSrc = cameraSound; // Use the imported sound file
let stream: MediaStream | null = null; // To hold the media stream

const cameraState = ref("start");

const provinces = ref<{ provCode: string; provDescription: string }[]>([]);
const municipalities = ref<
    { citymunCode: string; citymunDescription: string }[]
>([]);

const selectedProvince = ref<string | null>(null);
const selectedMunicipality = ref<string | null>(null);
const selectedBarangay = ref<string | null>(null);

const barangays = ref<{ brgyCode: string; brgyDescription: string }[]>([]);

const startCamera = async () => {
    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            stream = await navigator.mediaDevices.getUserMedia({
                video: true,
            });
            if (video.value) {
                video.value.srcObject = stream;
                cameraState.value = "capture";
            }
        } else {
            console.error("getUserMedia not supported");
        }
    } catch (error) {
        console.error("Error accessing camera: ", error);
    }
};

const capturePhoto = () => {
    if (video.value && canvas.value) {
        const context = canvas.value.getContext("2d");
        if (context) {
            // Set canvas dimensions to match the video
            canvas.value.width = video.value.videoWidth;
            canvas.value.height = video.value.videoHeight;

            // Draw the video frame onto the canvas
            context.drawImage(video.value, 0, 0);

            photo.value = canvas.value.toDataURL("image/jpeg");

            // Get base64 encoded image data
            const dataUrl = canvas.value.toDataURL("image/jpeg");

            // Convert base64 to Blob
            const blob = dataURLToBlob(dataUrl);

            // If you specifically need a File object, convert the Blob to a File
            profilePhoto.value = new File([blob], "photo.jpeg", {
                type: "image/jpeg",
            });

            // Play the capture sound
            const audioElement = document.querySelector(
                "audio"
            ) as HTMLAudioElement;
            if (audioElement) {
                audioElement.play();
            }

            // Set camera state to done
            cameraState.value = "done";

            // Stop all video tracks to release the camera
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
            }
        }
    }
};

const retakePhoto = () => {
    cameraState.value = "capture";
    if (video.value) {
        // Clear the previous photo
        photo.value = null;
        //profilePhoto.value = null;

        // Restart the camera
        startCamera();
    }
};

// Helper function to convert a base64-encoded Data URL to a Blob
function dataURLToBlob(dataUrl: string): Blob {
    const byteString = atob(dataUrl.split(",")[1]);
    const mimeString = dataUrl.split(",")[0].split(":")[1].split(";")[0];
    const buffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(buffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([uintArray], { type: mimeString });
}

const user = usePage().props.auth.user;

const form = useForm({
    firstname: user.profile.firstname,
    middlename: user.profile.middlename,
    lastname: user.profile.lastname,
    extension: user.profile.extension,

    region: user.profile.region,
    province: user.profile.province,
    municipality_city: user.profile.municipality_city,
    barangay: user.profile.barangay,
    street: user.profile.street,

    gender: user.profile.gender,
    birthdate: user.profile.birthdate,
    civil_status: user.profile.civil_status,
    blood_type: user.profile.blood_type,
    religion: user.profile.religion,
    tribe: user.profile.tribe,

    industry_sector: user.profile.industry_sector,
    occupation: user.profile.occupation,
    income_level: user.profile.income_level,

    affiliation: user.profile.affiliation,
    facebook: user.profile.facebook,
});

const submit = () => {
    form.patch(route("profile.update"), {
        onSuccess: () => {
            updatePhoto;
        },
    });
};
const updatePhoto = async () => {
    if (profilePhoto.value instanceof File) {
        const formData = new FormData();
        formData.append("avatar", profilePhoto.value);

        try {
            const response = await axios.post("/profile/photo", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            // Convert profilePhoto (File) to Base64 and save it in localStorage
            const reader = new FileReader();
            reader.onload = function () {
                const base64String = reader.result as string;
                if (base64String) {
                    // Save the base64 image to localStorage
                    localStorage.setItem("profilePhoto", base64String);
                    // Update the img tag with the saved image
                    updateImageSrc();
                }
            };
            reader.readAsDataURL(profilePhoto.value);

            if (response.data.success) {
                toast.success("Profile is successfully updated.");
            }
        } catch (error) {
            toast.error("Something went wrong, Please try again!");
        }
    }
};

const updateImageSrc = () => {
    const storedImage = localStorage.getItem("profilePhoto");

    if (storedImage) {
        photo.value = storedImage;
    }
};

const handleProvince = () => {
    selectedProvince.value = form.province;

    getMunicipalities(selectedProvince.value);
};

const handleMunicipality = () => {
    selectedMunicipality.value = form.municipality_city;

    getBarangay(selectedMunicipality.value);
};

const getProvinces = () => {
    axios
        .get("/provinces")
        .then(function (response) {
            // handle success
            provinces.value = [...response.data["provinces"]];
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
        .finally(function () {
            // always executed
        });
};

const getMunicipalities = (province: String) => {
    axios
        .get("/municipalities", {
            params: {
                province: province,
            },
        })
        .then(function (response) {
            // handle success
            municipalities.value = [...response.data["municipalities"]];
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
        .finally(function () {
            // always executed
        });
};

const getBarangay = (municipality: String) => {
    selectedBarangay.value = form.barangay;
    axios
        .get("/barangays", {
            params: {
                municipality: selectedMunicipality.value,
            },
        })
        .then(function (response) {
            // handle success
            barangays.value = [...response.data["barangays"]];
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
        .finally(function () {
            // always executed
        });
};

onMounted(() => {
    getProvinces();
    updateImageSrc();
});
</script>

<template>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Update your account's profile information.
        </p>
    </header>

    <form @submit.prevent="submit" class="mt-6 space-y-6">
        <div class="flex flex-col justify-center items-center">
            <h3 class="mb-2 text-gray-800">Capture Profile Photo</h3>
            <video
                v-show="cameraState == 'capture'"
                ref="video"
                autoplay
                class="border-4 border-gray-300 rounded-xl"
            ></video>

            <canvas ref="canvas" style="display: none"></canvas>
            <!-- Display the captured photo -->
            <img
                v-if="photo && cameraState !== 'capture'"
                :src="photo"
                alt="Captured Photo"
                class="border-2 border-gray-300 rounded-xl w-48"
            />

            <audio ref="captureSound" :src="captureSoundSrc"></audio>

            <SecondaryButton
                v-show="cameraState == 'start'"
                @click="startCamera"
                class="mt-4 w-52"
                :disabled="form.processing"
                >Open Camera</SecondaryButton
            >

            <SecondaryButton
                v-show="cameraState == 'capture'"
                @click="capturePhoto"
                class="mt-4 w-52"
                :disabled="form.processing"
                >Capture</SecondaryButton
            >

            <SecondaryButton
                v-show="cameraState == 'done'"
                @click="retakePhoto"
                class="mt-4 w-52"
                :disabled="form.processing"
                >Retake Photo</SecondaryButton
            >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <InputLabel for="firstname" value="Firstname" />

                <TextInput
                    id="firstname"
                    type="text"
                    class="mt-1 block w-full capitalize"
                    v-model="form.firstname"
                    required
                    autofocus
                    autocomplete="firstname"
                />

                <InputError class="mt-2" :message="form.errors.firstname" />
            </div>

            <div>
                <InputLabel for="middlename" value="Middlename" />

                <TextInput
                    id="middlename"
                    type="text"
                    class="mt-1 block w-full capitalize"
                    v-model="form.middlename"
                    required
                    autofocus
                    autocomplete="middlename"
                />

                <InputError class="mt-2" :message="form.errors.middlename" />
            </div>

            <div>
                <InputLabel for="lastname" value="Lastname" />

                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full capitalize"
                    v-model="form.lastname"
                    required
                    autofocus
                    autocomplete="lastname"
                />

                <InputError class="mt-2" :message="form.errors.lastname" />
            </div>

            <div>
                <InputLabel for="extension" value="Extension" />

                <select
                    id="extension"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.extension"
                    autofocus
                >
                    <option selected value="">None</option>
                    <option value="sr.">Sr.</option>
                    <option value="jr.">Jr.</option>
                    <option value="iii">III</option>
                    <option value="iv">IV</option>
                    <option value="v">V</option>
                </select>

                <InputError class="mt-2" :message="form.errors.extension" />
            </div>
        </div>

        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>
        <p class="font-medium text-lg">Address</p>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <InputLabel for="province" value="Province" />

                <select
                    @change="handleProvince()"
                    id="province"
                    class="mt-1 block w-full rounded shadow-sm border border-gray-300"
                    v-model="form.province"
                    required
                    autofocus
                >
                    <option
                        v-for="province in provinces"
                        :key="province.provCode"
                        :value="province.provCode"
                    >
                        {{ province.provDescription }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.province" />
            </div>

            <div>
                <InputLabel for="municipality" value="Municipality" />

                <select
                    @change="handleMunicipality()"
                    id="municipality"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.municipality_city"
                    required
                    autofocus
                >
                    <option
                        v-for="municipality in municipalities"
                        :key="municipality.citymunCode"
                        :value="municipality.citymunCode"
                    >
                        {{ municipality.citymunDescription }}
                    </option>
                </select>

                <InputError
                    class="mt-2"
                    :message="form.errors.municipality_city"
                />
            </div>

            <div>
                <InputLabel for="barangay" value="Barangay" />

                <select
                    id="barangay"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.barangay"
                    required
                    autofocus
                >
                    <option
                        v-for="barangay in barangays"
                        :key="barangay.brgyCode"
                        :value="barangay.brgyCode"
                    >
                        {{ barangay.brgyDescription }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.barangay" />
            </div>

            <div>
                <InputLabel for="street" value="Street" />

                <TextInput
                    id="street"
                    type="text"
                    class="mt-1 block w-full capitalize"
                    v-model="form.street"
                    required
                    autocomplete="street"
                />

                <InputError class="mt-2" :message="form.errors.street" />
            </div>
        </div>

        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

        <p class="font-medium text-lg">Personal Information</p>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <InputLabel for="gender" value="Gender" />

                <select
                    id="gender"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.gender"
                    required
                    autofocus
                >
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>

                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div>
                <InputLabel for="birthdate" value="Birthdate" />

                <input
                    type="date"
                    id="birthdate"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.birthdate"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.birthdate" />
            </div>

            <div>
                <InputLabel for="civil_status" value="Civil Status" />

                <select
                    id="civil_status"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.civil_status"
                    required
                    autofocus
                >
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widow">Widow</option>
                </select>

                <InputError class="mt-2" :message="form.errors.civil_status" />
            </div>

            <div>
                <InputLabel for="blood_type" value="Blood Type" />

                <select
                    id="blood_type"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.blood_type"
                    required
                    autofocus
                >
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="Bombay (Oh)">Bombay (Oh)</option>
                    <option value="Rh-null">Rh-null</option>
                    <option value="A1B+">A1B+</option>
                    <option value="A1B-">A1B-</option>
                    <option value="A3B+">A3B+</option>
                    <option value="A3B-">A3B-</option>
                </select>

                <InputError class="mt-2" :message="form.errors.blood_type" />
            </div>

            <div>
                <InputLabel for="religion" value="Religion" />

                <select
                    id="religion"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.religion"
                    required
                    autofocus
                >
                    <option value="catholicism">Catholicism</option>
                    <option value="protestantism">Protestantism</option>
                    <option value="islam">Islam</option>
                    <option value="mormonism">Mormonism</option>
                    <option value="jehovahs-witness">Jehovah's Witness</option>
                    <option value="other">Other</option>
                </select>

                <InputError class="mt-2" :message="form.errors.religion" />
            </div>

            <div>
                <InputLabel for="tribe" value="Tribe" />

                <select
                    id="tribe"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.tribe"
                    required
                    autofocus
                >
                    <option value="tagalog">Tagalog</option>
                    <option value="cebuano">Cebuano</option>
                    <option value="ilocano">Ilocano</option>
                    <option value="bisaya">Bisaya</option>
                    <option value="hiligaynon">Hiligaynon</option>
                    <option value="bicolano">Bicolano</option>
                    <option value="kapampangan">Kapampangan</option>
                    <option value="pangasinense">Pangasinense</option>
                    <option value="waray">Waray</option>
                    <option value="maranao">Maranao</option>
                    <option value="sama">Sama</option>
                    <option value="tausug">Tausug</option>
                    <option value="yakan">Yakan</option>
                    <option value="ifugao">Ifugao</option>
                    <option value="kalinga">Kalinga</option>
                    <option value="bontoc">Bontoc</option>
                    <option value="igurot">Igorot</option>
                    <option value="lumad">Lumad</option>
                    <option value="badjao">Badjao</option>
                    <option value="ivatan">Ivatan</option>
                    <option value="mangyan">Mangyan</option>
                    <option value="palawan">Palawan</option>
                    <option value="other">Other</option>
                </select>

                <InputError class="mt-2" :message="form.errors.tribe" />
            </div>
        </div>

        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>
        <p class="font-medium text-lg">Socioeconomic Information</p>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <InputLabel for="industry_sector" value="Industry/Sector" />

                <select
                    id="tribe"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.industry_sector"
                    required
                    autofocus
                >
                    <option value="agriculture">Agriculture</option>
                    <option value="arts-culture">Arts and Culture</option>
                    <option value="business-process-outsourcing">
                        BPO/Call Center
                    </option>
                    <option value="construction">Construction</option>
                    <option value="education">Education</option>
                    <option value="finance-banking">Finance and Banking</option>
                    <option value="fishing">Fishing</option>
                    <option value="government">Government</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="information-technology">
                        Information Technology
                    </option>
                    <option value="legal-services">Legal Services</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="media-communications">
                        Media and Communications
                    </option>
                    <option value="mining">Mining</option>
                    <option value="non-profit">Non-Profit and NGOs</option>
                    <option value="research-development">
                        Research and Development
                    </option>
                    <option value="real-estate">Real Estate</option>
                    <option value="retail-wholesale">
                        Wholesale and Retail Trade
                    </option>
                    <option value="tourism">Tourism and Hospitality</option>
                    <option value="transportation">
                        Transportation and Logistics
                    </option>
                    <option value="other">Other</option>
                </select>

                <InputError
                    class="mt-2"
                    :message="form.errors.industry_sector"
                />
            </div>

            <div>
                <InputLabel for="occupation" value="Occupation" />

                <select
                    id="occupation"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.occupation"
                    required
                    autofocus
                >
                    <option value="accountant">Accountant</option>
                    <option value="actor">Actor</option>
                    <option value="architect">Architect</option>
                    <option value="artist">Artist</option>
                    <option value="baker">Baker</option>
                    <option value="business-analyst">Business Analyst</option>
                    <option value="chef">Chef</option>
                    <option value="civil-engineer">Civil Engineer</option>
                    <option value="doctor">Doctor</option>
                    <option value="electrician">Electrician</option>
                    <option value="engineer">Engineer</option>
                    <option value="farmer">Farmer</option>
                    <option value="teacher">Teacher</option>
                    <option value="nurse">Nurse</option>
                    <option value="software-developer">
                        Software Developer
                    </option>
                    <option value="web-designer">Web Designer</option>
                    <option value="graphic-designer">Graphic Designer</option>
                    <option value="journalist">Journalist</option>
                    <option value="marketing-specialist">
                        Marketing Specialist
                    </option>
                    <option value="mechanic">Mechanic</option>
                    <option value="pharmacist">Pharmacist</option>
                    <option value="police-officer">Police Officer</option>
                    <option value="sales-representative">
                        Sales Representative
                    </option>
                    <option value="scientist">Scientist</option>
                    <option value="social-worker">Social Worker</option>
                    <option value="waiter">Waiter</option>
                    <option value="other">Other</option>
                </select>

                <InputError class="mt-2" :message="form.errors.occupation" />
            </div>

            <div>
                <InputLabel for="income_level" value="Income Level" />

                <select
                    id="income_level"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    v-model="form.income_level"
                    required
                    autofocus
                >
                    <option value="below-10000">Below ₱10,000</option>
                    <option value="10000-20000">₱10,000 - ₱20,000</option>
                    <option value="20001-30000">₱20,001 - ₱30,000</option>
                    <option value="30001-50000">₱30,001 - ₱50,000</option>
                    <option value="50001-100000">₱50,001 - ₱100,000</option>
                    <option value="above-100000">Above ₱100,000</option>
                    <option value="variable-income">Variable Income</option>
                    <option value="other">Other</option>
                </select>

                <InputError class="mt-2" :message="form.errors.income_level" />
            </div>
        </div>

        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>
        <p class="font-medium text-lg">
            Other <small class="text-gray-600">(optional)</small>
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <InputLabel for="affiliation" value="Affiliation" />

                <TextInput
                    id="street"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    v-model="form.affiliation"
                    autocomplete="affiliation"
                />

                <InputError class="mt-2" :message="form.errors.affiliation" />
            </div>

            <div>
                <InputLabel for="facebook" value="Facebook Profile" />

                <TextInput
                    id="facebook"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.facebook"
                    autocomplete="facebook"
                />

                <InputError class="mt-2" :message="form.errors.facebook" />
            </div>
            <div>
                <InputLabel for="affiliation" value="Affiliation" />

                <TextInput
                    id="street"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    v-model="form.affiliation"
                    autocomplete="affiliation"
                />

                <InputError class="mt-2" :message="form.errors.affiliation" />
            </div>

            <div>
                <InputLabel for="facebook" value="Facebook Profile" />

                <TextInput
                    id="facebook"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.facebook"
                    autocomplete="facebook"
                />

                <InputError class="mt-2" :message="form.errors.facebook" />
            </div>
        </div>

        <div
            class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4"
        >
            <Link
                class="w-full sm:w-52 mt-0 sm:mt-4"
                :href="route('profile.index')"
            >
                <SecondaryButton
                    @click="updatePhoto"
                    :disabled="form.processing"
                    >Back</SecondaryButton
                >
            </Link>
            <PrimaryButton
                class="sm:w-52 mt-4"
                @click="updatePhoto"
                :disabled="form.processing"
                >Save</PrimaryButton
            >

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >
                    Saved.
                </p>
            </Transition>
        </div>
    </form>
</template>

export interface Profile {
    lastname: string;
    firstname: string;
    middlename: string;
    extension: string;
    precinct_number: string;
    avatar: string;
    id_type: string;
    id_card_front: string;
    id_card_back: string;
    region: string;
    province: string;
    municipality_city: string;
    barangay: string;
    street: string;
    gender: string;
    birthdate: string;
    civil_status: string;
    blood_type: string;
    religion: string;
    tribe: string;
    industry_sector: string;
    occupation: string;
    income_level: string;
    affiliation: string;
    facebook: string;
}
export interface User {
    code: string;
    phone: string;
    points: number;
    level: number;
    profile: Profile;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
};

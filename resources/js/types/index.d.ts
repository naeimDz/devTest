// resources/js/types/index.ts

export interface Permission {
    id: number
    name: string
    module: string
    description?: string
  }
  
  export interface Role {
    id: number
    name: string
    description?: string
    permissions: Permission[]
  }
  
  export interface User {
    id: number
    name: string
    email: string
    role_id: number
    role: Role
  }
  
export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User | null;
        role: Role | null;
        permissions: Permission[];
    };
};

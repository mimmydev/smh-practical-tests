import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

// Industry options that match the backend validation
export const INDUSTRY_OPTIONS = [
  { value: 'technology', label: 'Technology' },
  { value: 'finance', label: 'Finance' },
  { value: 'healthcare', label: 'Healthcare' },
  { value: 'education', label: 'Education' },
  { value: 'manufacturing', label: 'Manufacturing' },
  { value: 'retail', label: 'Retail' },
  { value: 'consulting', label: 'Consulting' },
  { value: 'media', label: 'Media' },
  { value: 'government', label: 'Government' },
  { value: 'non_profit', label: 'Non-Profit' },
  { value: 'other', label: 'Other' }
] as const

// Booth preference options that match the backend validation
export const BOOTH_PREFERENCE_OPTIONS = [
  { value: 'premium', label: 'Premium Booth' },
  { value: 'standard', label: 'Standard Booth' },
  { value: 'corner', label: 'Corner Booth' },
  { value: 'island', label: 'Island Booth' }
] as const

// Phone number regex that matches the backend validation
const phoneRegex = /^[\+]?[0-9\-\(\)\s]+$/

// Zod schema that matches Laravel CreateExhibitorRequest validation
const exhibitorRegistrationSchema = z.object({
  name: z
    .string()
    .min(1, 'Please provide your company name.')
    .max(255, 'Company name is too long (maximum 255 characters).'),
  
  description: z
    .string()
    .min(50, 'Please provide a more detailed description (at least 50 characters).')
    .max(2000, 'Company description is too long. Please keep it under 2000 characters.'),
  
  contact_email: z
    .string()
    .min(1, 'Please provide a contact email address.')
    .email('Please provide a valid email address.'),
  
  phone: z
    .string()
    .min(1, 'Please provide a contact phone number.')
    .regex(phoneRegex, 'Please provide a valid phone number format.'),
  
  website: z
    .string()
    .optional()
    .refine((val) => !val || z.string().url().safeParse(val).success, {
      message: 'Please provide a valid website URL.'
    }),
  
  industry: z
    .enum(['technology', 'finance', 'healthcare', 'education', 'manufacturing', 'retail', 'consulting', 'media', 'government', 'non_profit', 'other'], {
      message: 'Please select a valid industry from the list.'
    }),
  
  address: z
    .string()
    .min(1, 'Please provide your company address.')
    .max(500, 'Address is too long (maximum 500 characters).'),
  
  booth_preferences: z
    .array(z.enum(['premium', 'standard', 'corner', 'island']))
    .optional()
    .default([])
})

export type ExhibitorRegistrationFormData = z.infer<typeof exhibitorRegistrationSchema>

// Export the typed schema for vee-validate
export const exhibitorRegistrationFormSchema = toTypedSchema(exhibitorRegistrationSchema)

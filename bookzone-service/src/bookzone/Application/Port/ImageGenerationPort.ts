export interface ImageGenerationPort {
  generateImage(prompt: string): Promise<string>
}
